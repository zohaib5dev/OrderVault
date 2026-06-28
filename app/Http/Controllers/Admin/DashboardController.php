<?php
// app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get date range for growth calculations
            $currentMonthStart = Carbon::now()->startOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            // Stats
            $stats = $this->getStats($currentMonthStart, $lastMonthStart, $lastMonthEnd);

            // Recent Orders (all orders)
            $recentOrders = $this->getRecentOrders();

            // My Orders (admin's orders)
            $myOrders = $this->getMyOrders($request->user());

            // Recent Vendors
            $recentVendors = $this->getRecentVendors();

            // Recent Plan Purchases
            $recentInvoices = $this->getRecentInvoices();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_orders' => $recentOrders,
                    'my_orders' => $myOrders,
                    'recent_vendors' => $recentVendors,
                    'recent_plan_purchases' => $recentInvoices,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getStats($currentMonthStart, $lastMonthStart, $lastMonthEnd)
    {
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');


        $currentMonthRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$currentMonthStart, Carbon::now()])
            ->sum('total_amount');

        $lastMonthRevenue = Order::where('status', 'completed')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');

        $totalMyOrdersRevenue = Order::where('vendor_id', Auth::user()->vendor->id)->where('status', 'completed')
            ->sum('total_amount');

        $currentMonthMyOrdersRevenue = Order::where('vendor_id', Auth::user()->vendor->id)->where('status', 'completed')
            ->whereBetween('created_at', [$currentMonthStart, Carbon::now()])
            ->sum('total_amount');

        $lastMonthMyOrdersRevenue = Order::where('vendor_id', Auth::user()->vendor->id)->where('status', 'completed')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');

        $revenueGrowth = $this->calculateGrowth($lastMonthRevenue, $currentMonthRevenue);

        $revenueMyOrdersGrowth = $this->calculateGrowth($lastMonthMyOrdersRevenue, $currentMonthMyOrdersRevenue);

        $totalOrders = Order::count();

        $currentMonthOrders = Order::whereBetween('created_at', [$currentMonthStart, Carbon::now()])->count();

        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $ordersGrowth = $this->calculateGrowth($lastMonthOrders, $currentMonthOrders);

        $myOrdersGrowth = $this->calculateGrowth($currentMonthMyOrdersRevenue, $lastMonthMyOrdersRevenue);

        $myOrders = Order::where('vendor_id', Auth::user()->vendor->id)->count();

        $myOrdersPending = Order::where('vendor_id', Auth::user()->vendor->id)
            ->where('status', 'pending')
            ->count();

        $planInvoices = Invoice::count();
        $planRevenue = Invoice::sum('amount');

        // Active Subscriptions
        $activeSubscriptions = Vendor::where('subscription_status', 'active')
            //->where('type', 'subscription')
            ->where(function ($query) {
                $query->whereNull('subscription_ends_at')
                    ->orWhere('subscription_ends_at', '>', Carbon::now());
            })
            ->count();

        $totalVendors = Vendor::count();

        $totalProducts = Product::count();

        $totalCustomers = User::where('role', 'customer')->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_myOrders_revenue' => $totalMyOrdersRevenue,
            'current_month_revenue' => $currentMonthRevenue,
            'revenue_myOrders_growth' => $revenueMyOrdersGrowth,
            'last_month_revenue' => $lastMonthRevenue,
            'revenue_growth' => round($revenueGrowth, 2),
            'total_orders' => $totalOrders,
            'current_month_orders' => $currentMonthOrders,
            'last_month_orders' => $lastMonthOrders,
            'orders_growth' => round($ordersGrowth, 2),
            'myOrders_growth' => round($myOrdersGrowth, 2),
            'my_orders' => $myOrders,
            'my_orders_pending' => $myOrdersPending,
            'plan_purchases' => $planInvoices,
            'plan_revenue' => $planRevenue,
            'active_subscriptions' => $activeSubscriptions,
            'total_vendors' => $totalVendors,
            'total_products' => $totalProducts,
            'total_customers' => $totalCustomers,
        ];
    }

    /**
     * Get recent orders
     */
    private function getRecentOrders()
    {
        return Order::with(['customer' => function ($query) {
            $query->select('id', 'name', 'email');
        }])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer?->name ?? 'Guest',
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ];
            });
    }

    /**
     * Get my orders (admin's orders)
     */
    private function getMyOrders($user)
    {
        return Order::where('vendor_id', $user->id)
            ->with(['vendor' => function ($query) {
                $query->select('id', 'business_name');
            }])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'vendor_name' => $order->vendor?->business_name ?? 'N/A',
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ];
            });
    }

    /**
     * Get recent vendors
     */
    private function getRecentVendors()
    {
        return Vendor::with(['user' => function ($query) {
            $query->select('id', 'name', 'email');
        }])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($vendor) {
                return [
                    'id' => $vendor->id,
                    'business_name' => $vendor->business_name,
                    'name' => $vendor->user?->name,
                    'email' => $vendor->user?->email,
                    'is_active' => $vendor->is_active,
                    'created_at' => $vendor->created_at,
                ];
            });
    }

    /**
     * Get recent plan purchases
     */
    private function getRecentInvoices()
    {
        return Invoice::with(['plan', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($purchase) {
                return [
                    'id' => $purchase->id,
                    'plan_name' => $purchase->plan?->name ?? 'Unknown Plan',
                    'vendor_name' => $purchase->vendor?->business_name ?? 'Unknown Vendor',
                    'amount' => $purchase->amount,
                    'type' => $purchase->type ?? 'one_time',
                    'purchased_at' => $purchase->created_at,
                ];
            });
    }

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth($previous, $current)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    /**
     * Get dashboard overview for quick stats (alternative endpoint)
     */
    public function overview()
    {
        try {
            $stats = [
                'total_vendors' => Vendor::count(),
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'active_vendors' => Vendor::where('is_active', true)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard overview error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load overview data'
            ], 500);
        }
    }

    /**
     * Get revenue chart data
     */
    public function revenueChart(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $startDate = Carbon::now()->subDays($days);

            $revenueData = Order::where('status', 'completed')
                ->where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'total' => $item->total,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $revenueData
            ]);
        } catch (\Exception $e) {
            \Log::error('Revenue chart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load revenue chart data'
            ], 500);
        }
    }

    /**
     * Get orders chart data
     */
    public function ordersChart(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $startDate = Carbon::now()->subDays($days);

            $ordersData = Order::where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total')
                )
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'total' => $item->total,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $ordersData
            ]);
        } catch (\Exception $e) {
            \Log::error('Orders chart error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders chart data'
            ], 500);
        }
    }

    /**
     * Get top selling products
     */
    public function topProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);

            $topProducts = DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.total_price) as total_revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_revenue', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'total_quantity' => $item->total_quantity,
                        'total_revenue' => $item->total_revenue,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $topProducts
            ]);
        } catch (\Exception $e) {
            \Log::error('Top products error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load top products'
            ], 500);
        }
    }

    /**
     * Get order status distribution
     */
    public function orderStatusDistribution()
    {
        try {
            $distribution = Order::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->map(function ($item) {
                    return [
                        'status' => $item->status,
                        'count' => $item->count,
                        'label' => ucfirst($item->status),
                        'color' => $this->getStatusColor($item->status),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);
        } catch (\Exception $e) {
            \Log::error('Order status distribution error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load order status distribution'
            ], 500);
        }
    }

    /**
     * Get status color for chart
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => '#F59E0B',
            'processing' => '#3B82F6',
            'shipped' => '#8B5CF6',
            'delivered' => '#10B981',
            'completed' => '#10B981',
            'cancelled' => '#EF4444',
            'failed' => '#EF4444',
        ];

        return $colors[$status] ?? '#6B7280';
    }


    public function vendorSettings()
    {
        return response()->json(Auth::user()->vendor);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:50',
            'business_website' => 'nullable|url|max:255',
            'business_address' => 'required|string',
            'tax_type' => 'required|in:percentage,fixed',
            'tax_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_rate' => 'required|numeric|min:0',
            'registration_number' => 'nullable|string|max:100',
            'currency' => 'string|max:3',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max
        ]);

        $vendor = Auth::user()->vendor;

        $data = [
            'business_name' => $request->business_name,
            'business_email' => $request->business_email,
            'business_phone' => $request->business_phone,
            'business_website' => $request->business_website,
            'business_address' => $request->business_address,
            'tax_type' => $request->tax_type,
            'tax_rate' => $request->tax_rate,
            'commission_type' => $request->commission_type,
            'commission_rate' => $request->commission_rate,
            'registration_number' => $request->registration_number,
            'currency' => $request->currency,
        ];

        // Handle logo upload
        if ($request->hasFile('business_logo')) {

            // Delete old logo
            if ($vendor->business_logo && Storage::disk('public')->exists($vendor->business_logo)) {
                Storage::disk('public')->delete($vendor->business_logo);
            }

            $file = $request->file('business_logo');

            $filename = \Illuminate\Support\Str::slug($vendor->business_name ?: 'vendor')
                . '-' . $vendor->id
                . '-' . time()
                . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('vendor-logos', $filename, 'public');

            $data['business_logo'] = $path; // e.g. vendor-logos/my-business-5-1750743215.png
        }

        $vendor->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'business_logo' => $vendor->business_logo ? asset('storage/' . $vendor->business_logo) : null
        ]);
    }


    public function createStripeAccount(Request $request)
    {
        try {
            $vendor = Vendor::where('user_id', Auth::id())->first();

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor not found'
                ], 404);
            }

            if ($vendor->stripe_account_id) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor already has a Stripe account',
                    'data' => ['stripe_account_id' => $vendor->stripe_account_id]
                ]);
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $account = Account::create([
                'type' => 'express',
                'country' => 'US',
                'email' => $vendor->email ?? Auth::user()->email,
                'business_type' => 'individual',
                'business_profile' => [
                    'name' => $vendor->business_name,
                    'url' => $vendor->website ?? config('app.url'),
                ],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'metadata' => [
                    'vendor_id' => $vendor->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            $vendor->stripe_account_id = $account->id;
            $vendor->save();

            // Create account link for onboarding
            $accountLink = AccountLink::create([
                'account' => $account->id,
                'refresh_url' => route('stripe.refresh', ['vendor_id' => $vendor->id]),
                'return_url' => route('stripe.return', ['vendor_id' => $vendor->id]),
                'type' => 'account_onboarding',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stripe account created successfully',
                'data' => [
                    'stripe_account_id' => $account->id,
                    'onboarding_url' => $accountLink->url,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Create Stripe account error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Stripe account: ' . $e->getMessage()
            ], 500);
        }
    }
}
