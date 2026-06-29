<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\OrderItem; 
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\PaymentIntent;


class CustomerController extends Controller
{
    public function dashboard()
    {
        try {
            $customer = Auth::user();

            // Get customer orders
            $orders = Order::where('customer_id', $customer->id)
                ->with(['items.product'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate stats
            $totalOrders = $orders->count();
            $pendingOrders = $orders->where('status', 'pending')->count();
            $completedOrders = $orders->where('status', 'completed')->count();
            $totalSpent = $orders->where('payment_status', 'paid')->sum('total_amount');

            // Get previous month stats for growth calculation
            $lastMonthOrders = Order::where('customer_id', $customer->id)
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();

            $lastMonthSpent = Order::where('customer_id', $customer->id)
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->sum('total_amount');

            $thisMonthOrders = Order::where('customer_id', $customer->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();

            $thisMonthSpent = Order::where('customer_id', $customer->id)
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total_amount');

            // Calculate growth percentages
            $ordersGrowth = $lastMonthOrders > 0
                ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1)
                : ($thisMonthOrders > 0 ? 100 : 0);

            $spentGrowth = $lastMonthSpent > 0
                ? round((($thisMonthSpent - $lastMonthSpent) / $lastMonthSpent) * 100, 1)
                : ($thisMonthSpent > 0 ? 100 : 0);


            // Get products the customer has purchased
            $productIds = OrderItem::whereIn('order_id', $orders->pluck('id'))
                ->pluck('product_id')
                ->unique()
                ->filter();
            $totalProducts = $productIds->count();

            // Calculate pending orders growth
            $lastMonthPending = Order::where('customer_id', $customer->id)
                ->where('status', 'pending')
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();

            $thisMonthPending = Order::where('customer_id', $customer->id)
                ->where('status', 'pending')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();

            $pendingGrowth = $lastMonthPending > 0
                ? round((($thisMonthPending - $lastMonthPending) / $lastMonthPending) * 100, 1)
                : ($thisMonthPending > 0 ? 100 : 0);

            // Calculate completed orders growth
            $lastMonthCompleted = Order::where('customer_id', $customer->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();

            $thisMonthCompleted = Order::where('customer_id', $customer->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();

            $completedGrowth = $lastMonthCompleted > 0
                ? round((($thisMonthCompleted - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1)
                : ($thisMonthCompleted > 0 ? 100 : 0);

            $stats = [
                'total_orders' => $totalOrders,
                'pending_orders' => $pendingOrders,
                'completed_orders' => $completedOrders,
                'total_spent' => $totalSpent,
                'orders_growth' => $ordersGrowth,
                'pending_growth' => $pendingGrowth,
                'completed_growth' => $completedGrowth,
                'spent_growth' => $spentGrowth,
                'total_products' => $totalProducts,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'orders' => $orders->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'order_number' => $order->order_number ?? 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                            'total_amount' => $order->total_amount,
                            'status' => $order->status,
                            'payment_status' => $order->payment_status,
                            'customer_name' => $order->customer_name ?? 'Guest',
                            'customer_email' => $order->customer_email ?? '',
                            'created_at' => $order->created_at,
                            'created_at_human' => $order->created_at->diffForHumans(),
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Customer dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getOrder($id)
    {
        try {
            $customer = Auth::user();
            $order = Order::where('customer_id', $customer->id)
                ->where('id', $id)
                ->with(['items.product', 'items.product'])
                ->first();
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            $order->load(['customer', 'items.product']);

            return response()->json(['order' => $order, 'stripe_key' => config('services.stripe.key')]);
        } catch (\Exception $e) {
            Log::error('Get order error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load order details'
            ], 500);
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


    public function payInvoice(Request $request, $id)
    {
        try {
            $customer = Auth::user();
            $order = Order::where('customer_id', $customer->id)
                ->where('id', $id)
                ->with(['items.product'])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already paid'
                ], 400);
            }

            $request->validate([
                'payment_method_id' => 'required|string',
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));


            // Calculate amounts
            $totalAmount = $order->total_amount;
         
            // Convert to cents for Stripe
            $totalCents = (int) round($totalAmount * 100);

            $stripeCustomer = $this->getOrCreateStripeCustomer(Auth::user());

            DB::beginTransaction();

            try {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $totalCents,
                    'currency' => 'usd',
                    'customer' => $stripeCustomer->id,
                    'confirm' => true,
                    'payment_method' => $request->payment_method_id,
                    'off_session' => false,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_id' => $customer->id, 
                        'amount' => $totalAmount,  
                        'payment_type' => 'direct_to_admin'
                    ],
                ]);

                // If payment succeeded
                if ($paymentIntent->status === 'succeeded') {

                    $order->payment_status = 'paid';

                    $order->save();
                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful!',
                        'data' => [
                            'order' => $order,
                        ]
                    ]);
                }

                // If payment requires additional action (3D Secure)
                if (
                    $paymentIntent->status === 'requires_action' ||
                    $paymentIntent->status === 'requires_confirmation'
                ) {
                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'requires_action' => true,
                        'payment_intent_client_secret' => $paymentIntent->client_secret,
                        'message' => 'Additional authentication required',
                      ]);
                }

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed. Please try again.',
                    'payment_intent_status' => $paymentIntent->status
                ], 400);
            } catch (\Stripe\Exception\CardException $e) {
                DB::rollBack();
                Log::error('Stripe Card Error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Card error: ' . $e->getError()->message
                ], 400);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Payment error: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Pay invoice error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }


    protected function getOrCreateStripeCustomer($user)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        if ($user->stripe_customer_id) {
            try {
                return Customer::retrieve($user->stripe_customer_id);
            } catch (\Exception $e) {

                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
                return $customer;
            }
        }
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);
        return $customer;
    }


    /**
     * Confirm payment after 3D Secure authentication
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            $customer = Auth::user();
            $order = Order::where('customer_id', $customer->id)
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            $request->validate([
                'payment_intent_id' => 'required|string',
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {

                try {
                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment confirmed successfully!',
                        'data' => ['order' => $order]
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not confirmed. Status: ' . $paymentIntent->status
            ], 400);
        } catch (\Exception $e) {
            Log::error('Confirm payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
