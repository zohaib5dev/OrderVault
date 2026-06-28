<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Get vendor analytics data.
     */
    public function analytics(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $previousStartDate = now()->subDays($days * 2);

        // Get orders for current period
        $currentPeriodOrders = Order::where('vendor_id', $vendor->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        // Get orders for previous period
        $previousPeriodOrders = Order::where('vendor_id', $vendor->id)
            ->whereBetween('created_at', [$previousStartDate, $startDate])
            ->get();

        // Calculate metrics
        $metrics = $this->calculateMetrics($currentPeriodOrders, $previousPeriodOrders);

        // Get chart data
        $revenueData = $this->getRevenueChartData($vendor->id, $startDate);
        $ordersData = $this->getOrdersChartData($vendor->id, $startDate);

        // Get top products
        $topProducts = $this->getTopProducts($vendor->id, $startDate);

        // Get order status distribution
        $orderStatusDistribution = $this->getOrderStatusDistribution($vendor->id, $startDate);

        // Get recent orders
        $recentOrders = $this->getRecentOrders($vendor->id);

        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => $metrics,
                'revenue_data' => $revenueData,
                'orders_data' => $ordersData,
                'top_products' => $topProducts,
                'order_status_distribution' => $orderStatusDistribution,
                'recent_orders' => $recentOrders
            ]
        ]);
    }

    /**
     * Calculate metrics from orders.
     */
    private function calculateMetrics($currentOrders, $previousOrders)
    {
        $totalRevenue = $currentOrders->where('payment_status', 'paid')->sum('total_amount');
        $previousRevenue = $previousOrders->sum('total_amount');
        $totalOrders = $currentOrders->count();
        $previousOrdersCount = $previousOrders->count();

        // Revenue growth
        $revenueGrowth = $previousRevenue > 0 
            ? round((($totalRevenue - $previousRevenue) / $previousRevenue) * 100, 1)
            : 0;

        // Orders growth
        $ordersGrowth = $previousOrdersCount > 0 
            ? round((($totalOrders - $previousOrdersCount) / $previousOrdersCount) * 100, 1)
            : 0;

        // Average order value
        $averageOrderValue = $totalOrders > 0 
            ? round($totalRevenue / $totalOrders, 2) 
            : 0;

        // Previous average order value
        $previousAOV = $previousOrdersCount > 0 
            ? round($previousRevenue / $previousOrdersCount, 2) 
            : 0;

        // AOV growth
        $aovGrowth = $previousAOV > 0 
            ? round((($averageOrderValue - $previousAOV) / $previousAOV) * 100, 1)
            : 0;

        // Conversion rate (would need visitor data from analytics)
        // For now, we'll use mock data or calculate from available data
        $conversionRate = 2.4; // This should be calculated from actual visitor data
        $conversionGrowth = 0.5; // This should be calculated from actual visitor data

        return [
            'total_revenue' => $totalRevenue,
            'revenue_growth' => $revenueGrowth,
            'total_orders' => $totalOrders,
            'orders_growth' => $ordersGrowth,
            'average_order_value' => $averageOrderValue,
            'aov_growth' => $aovGrowth,
            'conversion_rate' => $conversionRate,
            'conversion_growth' => $conversionGrowth
        ];
    }

    /**
     * Get revenue data for chart.
     */
    private function getRevenueChartData($vendorId, $startDate)
    {
        $orders = Order::where('vendor_id', $vendorId)
        ->where('payment_status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            });

        $revenueData = [];
        foreach ($orders as $date => $ordersOnDate) {
            $revenueData[] = [
                'date' => $date,
                'revenue' => $ordersOnDate->sum('total_amount')
            ];
        }

        // Sort by date
        usort($revenueData, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $revenueData;
    }

    /**
     * Get orders data for chart.
     */
    private function getOrdersChartData($vendorId, $startDate)
    {
        $orders = Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            });

        $ordersData = [];
        foreach ($orders as $date => $ordersOnDate) {
            $ordersData[] = [
                'date' => $date,
                'orders' => $ordersOnDate->count()
            ];
        }

        // Sort by date
        usort($ordersData, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $ordersData;
    }

    /**
     * Get top selling products.
     */
    private function getTopProducts($vendorId, $startDate)
    {
        // Get order items from orders in the period
        $orderItems = OrderItem::whereHas('order', function ($query) use ($vendorId, $startDate) {
            $query->where('vendor_id', $vendorId)
                ->where('created_at', '>=', $startDate);
        })
        ->with('product')
        ->get();

        // Group by product
        $productSales = $orderItems->groupBy('product_id')->map(function ($items) {
            $firstItem = $items->first();
            return [
                'product_id' => $firstItem->product_id,
                'product_name' => $firstItem->product_name,
                'revenue' => $items->sum('total_price'),
                'quantity' => $items->sum('quantity')
            ];
        });

        // Sort by revenue and take top 5
        $topProducts = $productSales->sortByDesc('revenue')->take(5)->values();

        // Calculate percentages
        $totalRevenue = $topProducts->sum('revenue');

        return $topProducts->map(function ($product) use ($totalRevenue) {
            return [
                'name' => $product['product_name'],
                'revenue' => $product['revenue'],
                'percentage' => $totalRevenue > 0 
                    ? round(($product['revenue'] / $totalRevenue) * 100) 
                    : 0
            ];
        });
    }

    /**
     * Get order status distribution.
     */
    private function getOrderStatusDistribution($vendorId, $startDate)
    {
        $orders = Order::where('vendor_id', $vendorId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $totalOrders = $orders->count();

        $statusColors = [
            'pending' => '#F59E0B',
            'processing' => '#3B82F6',
            'completed' => '#10B981',
            'cancelled' => '#EF4444',
            'shipped' => '#8B5CF6',
            'delivered' => '#10B981'
        ];

        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered'
        ];

        // Group by status
        $statusGroups = $orders->groupBy('status');

        $distribution = [];
        foreach ($statusGroups as $status => $ordersByStatus) {
            $distribution[] = [
                'status' => $status,
                'label' => $statusLabels[$status] ?? ucfirst($status),
                'count' => $ordersByStatus->count(),
                'percentage' => $totalOrders > 0 
                    ? round(($ordersByStatus->count() / $totalOrders) * 100, 1) 
                    : 0,
                'color' => $statusColors[$status] ?? '#6B7280'
            ];
        }

        // Sort by count descending
        usort($distribution, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        return $distribution;
    }

    /**
     * Get recent orders.
     */
    private function getRecentOrders($vendorId)
    {
        return Order::where('vendor_id', $vendorId)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer ? $order->customer->name : 'N/A',
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at
                ];
            });
    }

    /**
     * Get vendor dashboard stats.
     */
    public function dashboardStats()
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        // Get total orders
        $totalOrders = Order::where('vendor_id', $vendor->id)->count();

        // Get pending orders
        $pendingOrders = Order::where('vendor_id', $vendor->id)
            ->where('status', 'pending')
            ->count();

        // Get total revenue
        $totalRevenue = Order::where('vendor_id', $vendor->id)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Get active products
        $activeProducts = Product::where('vendor_id', $vendor->id)
            ->where('is_active', true)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'active_products' => $activeProducts,
                'pending_orders' => $pendingOrders
            ]
        ]);
    }

    /**
     * Get monthly revenue trend.
     */
    public function revenueTrend(Request $request)
    {
        $vendor = Auth::user()->vendor;
        
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $months = $request->get('months', 6);
        $startDate = now()->subMonths($months);

        $monthlyRevenue = Order::where('vendor_id', $vendor->id)
            ->where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m');
            })
            ->map(function ($orders, $month) {
                return [
                    'month' => $month,
                    'revenue' => $orders->sum('total_amount'),
                    'orders' => $orders->count()
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $monthlyRevenue
        ]);
    }
}