<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $orders = Order::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $orders->where('status', $request->status);
        }

        if ($request->has('payment_status') && $request->payment_status) {
            $orders->where('payment_status', $request->payment_status);
        }
        if ($request->has('search') && $request->search) {
            $orders->search($request->search);
        }

        $stats = [
            'total_orders' => Order::count(),
            'total_paid_orders' => Order::where('payment_status', 'paid')->count(),
            'total_pending_orders' => Order::where('status', 'pending')->count(),
            'total_completed_orders' => Order::where('status', 'completed')->count(),
        ];

        $perPage = $request->get('per_page', 10);
        $orders = $orders->paginate($perPage);

        return response()->json(['orders' => $orders, 'stats' => $stats]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|string',
            'payment_status' => 'nullable|in:pending,paid,failed',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Calculate totals
            $subtotal = 0;
            $commissionAmount = 0;
            $totalDiscount = 0;
            $orderItems = [];

            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);

                // Check stock
                if ($product->stock_quantity < $itemData['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $itemTotal = $itemData['price'] * $itemData['quantity'];
                $discountAmount = ($itemTotal * ($itemData['discount'] ?? 0)) / 100;
                $totalPrice = $itemTotal - $discountAmount;

                $subtotal += $itemTotal;
                $totalDiscount += $discountAmount;

                $orderItems[] = [
                    'product_id' => $itemData['product_id'],
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'total_price' => $totalPrice,
                    'discount' => $itemData['discount'],
                ];
            }



            $totalAmount = $request->total + $commissionAmount;
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $request->customer_id,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'subtotal' => $subtotal,
                'tax_amount' => $request->tax_amount,
                'shipping_cost' => $request->shipping_cost,
                'discount_amount' => $totalDiscount,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'currency' => 'usd',
                'status' => 'pending',
                'due_at' => $request->due_date,
                'payment_status' => $request->payment_status ?? 'pending',
                'fulfillment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'metadata' => [
                    'created_by' => $user->id,
                    'customer_email' => User::find($request->customer_id)->email,
                    'items_count' => count($orderItems)
                ]
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'discount_amount' => $item['discount'] ?? 0,
                    'total_price' => $item['total_price'],
                ]);

                // Reduce stock
                Product::where('id', $item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'data' => $e->getMessage()]);
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.product']);

        return response($order);
    }


    public function destroy($id)
    {

        try {
            $order = Order::find($id);
            // Check if order can be deleted
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                throw new \Exception('Only pending or cancelled orders can be deleted.');
            }
            // Restore stock
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
            }

            $order->items()->delete();
            $order->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded,partially_paid',
            // 'fulfillment_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Update order
            $order->update([
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'shipping_address' => $request->shipping_address ?? $order->shipping_address,
                'billing_address' => $request->billing_address ?? $order->billing_address,
                'notes' => $request->notes ?? $order->notes,
            ]);

            // Update timestamps based on status
            if ($request->status === 'paid' && !$order->paid_at) {
                $order->update(['paid_at' => now()]);
            }

            if ($request->status === 'shipped' && !$order->shipped_at) {
                $order->update(['shipped_at' => now()]);
            }



            // If order is cancelled, restore stock
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }

            // If order is refunded, update payment status
            if ($request->status === 'refunded') {
                $order->update(['payment_status' => 'refunded']);
            }

            DB::commit();

            return response()->json([
                'success' => true,

            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()

            ]);
        }
    }

    public function downloadInvoice(Request $request, $id)
    {
        $order = Order::with(['customer', 'items.product'])->findOrFail($id);
        $settings = BusinessSetting::first();
        $logoPath = null;
        if ($settings->logo) {
            $logoPath = public_path('storage/' . $settings->logo);
        }
        $data = [
            'invoice' => [
                'id' => $order->id,
                'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'date' => $order->created_at->format('F d, Y'),
            ],
            'settings' => $settings,
            'logoPath' => $logoPath,
            'order' => $order->toArray(),
            'customer' => $order->customer->toArray(),
            'items' => $order->items->toArray(),
            'shipping_address' => $order->shipping_address,
            'billing_address' => $order->billing_address,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('order.invoice', $data);


        // Generate filename
        $filename = "invoice-{$order->order_number}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Bulk update order status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled,refunded',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $orders = Order::whereIn('id', $request->order_ids);


            $orders->update(['status' => $request->status]);

            DB::commit();

            return back()->with('success', count($request->order_ids) . ' orders updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update orders: ' . $e->getMessage());
        }
    }
}
