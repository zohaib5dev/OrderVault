<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Vendor::with('user');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'LIKE', "%{$search}%")
                    ->orWhere('business_address', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'pending') {
                $query->where('is_active', false)->whereNull('approved_at');
            }
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $vendors = $query->paginate($perPage);

        // Add stats to each vendor
        $vendors->getCollection()->transform(function ($vendor) {
            return $this->formatVendorData($vendor);
        });

        // Stats
        $stats = [
            'total' => Vendor::count(),
            'active' => Vendor::where('is_active', true)->count(),
            'inactive' => Vendor::where('is_active', false)->count(),
            'active_subscriptions' => Vendor::where('subscription_status', 'active')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $vendors,
            'stats' => $stats
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // User validation
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8',

            // Vendor validation
            'business_name' => 'required|string|max:255|unique:vendors,business_name',
            'business_address' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'vendor',
                'email_verified_at' => now(),
            ]);

            $plan = Plan::find($request->plan_id);
            // Prepare vendor data
            $vendorData = [
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'business_address' => $request->business_address,
                'plan_id' => $request->plan_id,
                'commission_rate' => $request->commission_rate ?? 10.00,
                'is_active' => $request->is_active ?? true,
            ];

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '_' . $user->id . '.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs('vendor-logos', $logoName, 'public');
                $vendorData['business_logo'] = $logoPath;
            }

            // Create vendor account
            $vendor = Vendor::create($vendorData);

            $invoice = $this->createInvoice($vendor, $plan);

            return response()->json([
                'message' => 'Vendor created successfully',
                'id' => $vendor->id,
                'vendor' => $vendor->load('user'),
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create vendor: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $vendor = Vendor::with(['user', 'products.category', 'orders.customer', 'orders.items'])
            ->findOrFail($id);

        $orders = $vendor->orders()->with('customer')->latest()->get();
        $products = $vendor->products()->with('category')->latest()->get();

        // Statistics
        $statistics = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_products' => $products->count(),
            'status_distribution' => $orders->groupBy('status')->map->count(),
            'revenue_by_date' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($orders) {
                return $orders->sum('total_amount');
            })->map(function ($total, $date) {
                return ['date' => $date, 'total' => $total];
            })->values()
        ];

        return response()->json([
            'vendor' => $vendor,
            'orders' => $orders,
            'products' => $products,
            'statistics' => $statistics
        ]);
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::with('user')->findOrFail($id);
        $user = $vendor->user;

        $validator = Validator::make($request->all(), [
            // User validation
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8',

            // Vendor validation
            'business_name' => 'required|string|max:255|unique:vendors,business_name,' . $vendor->id,
            'business_address' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Update user
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update vendor
            $vendorData = [
                'business_name' => $request->business_name,
                'business_address' => $request->business_address,
                'plan_id' => $request->plan_id,
                'commission_rate' => $request->commission_rate,
                'is_active' => $request->is_active ?? true,
            ];

            // Handle logo
            if ($request->hasFile('logo')) {
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo);
                }

                $logo = $request->file('logo');
                $logoName = time() . '_' . $user->id . '.' . $logo->getClientOriginalExtension();
                $logoPath = $logo->storeAs('vendor-logos', $logoName, 'public');
                $vendorData['business_logo'] = $logoPath;
            } elseif ($request->input('remove_logo') === 'true') {
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo);
                }
                $vendorData['business_logo'] = null;
            }

            $vendor->update($vendorData);

            return response()->json([
                'message' => 'Vendor updated successfully',
                'id' => $vendor->id,
                'vendor' => $vendor->fresh()->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update vendor: ' . $e->getMessage()
            ], 500);
        }
    }


    public function toggleStatus($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $vendor->is_active = !$vendor->is_active;

        if ($vendor->is_active && !$vendor->approved_at) {
            // $vendor->approved_at = now();
        }

        $vendor->save();

        return response()->json([
            'success' => true,
            'message' => 'Vendor status updated successfully',
            'data' => [
                'is_active' => $vendor->is_active,
                'status' => $vendor->is_active ? 'active' : 'inactive'
            ]
        ]);
    }


    public function destroy($id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // Check if vendor has orders
        $hasOrders = $vendor->orders()->exists();
        if ($hasOrders) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete vendor with existing orders. Consider deactivating instead.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Delete vendor first
            $vendor->delete();

            // Delete associated user
            $user = User::find($vendor->user_id);
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vendor deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete vendor: ' . $e->getMessage()
            ], 500);
        }
    }




    private function createInvoice($vendor, $plan)
    {
        $dueDate = now()->addDays(7); // Due in 7 days
        $amount = $plan->price;

      
        $invoiceData = [
            'vendor_id' => $vendor->id,
            'plan_id' => $plan->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $amount,
            'subtotal' => $amount,
            'tax' => 0,
            'discount' => 0,
            'total' => $amount,
            'currency' => 'USD',
            'status' => 'pending',
            'due_date' => $dueDate,
            'type' => $plan->type,
            'notes' => 'Welcome to ' . $plan->name . ' plan. Please pay this invoice to activate your vendor account.',
            'items' => [
                [
                    'name' => $plan->name . ' Plan - Subscription',
                    'description' => $plan->description,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'total' => $amount,
                ]
            ]
        ];

        return Invoice::create($invoiceData);
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;

        return $prefix . '-' . $year . $month . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }


    private function formatVendorData($vendor)
    {
        $user = $vendor->user;

        // Get order stats
        $orderStats = DB::table('orders')
            ->where('vendor_id', $vendor->id)
            ->selectRaw('COUNT(*) as orders_count, SUM(total_amount) as revenue')
            ->first();

        return [
            'id' => $vendor->id,
            'user_id' => $vendor->user_id,
            'business_name' => $vendor->business_name,
            'business_address' => $vendor->business_address,
            'commission_rate' => $vendor->commission_rate,
            'is_active' => (bool) $vendor->is_active,
            'approved_at' => $vendor->approved_at,
            'created_at' => $vendor->created_at,
            'updated_at' => $vendor->updated_at,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
            ] : null,
            'orders_count' => $orderStats->orders_count ?? 0,
            'revenue' => $orderStats->revenue ?? 0,
        ];
    }
}
