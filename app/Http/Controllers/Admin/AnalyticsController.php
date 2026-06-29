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
    public function analytics(Request $request)
    {

        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        $previousStartDate = now()->subDays($days * 2);

        // Get orders for current period
        $currentPeriodOrders = Order::where('created_at', '>=', $startDate)
            ->get();

        // Get orders for previous period
        $previousPeriodOrders = Order::whereBetween('created_at', [$previousStartDate, $startDate])
            ->get();

        // Calculate metrics
        $metrics = $this->calculateMetrics($currentPeriodOrders, $previousPeriodOrders);

        // Get chart data
        $revenueData = $this->getRevenueChartData($startDate);
        $ordersData = $this->getOrdersChartData($startDate);

        // Get top products
        $topProducts = $this->getTopProducts($startDate);

        // Get order status distribution
        $orderStatusDistribution = $this->getOrderStatusDistribution($startDate);

        // Get recent orders
        $recentOrders = $this->getRecentOrders();

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
 
        return [
            'total_revenue' => $totalRevenue,
            'revenue_growth' => $revenueGrowth,
            'total_orders' => $totalOrders,
            'orders_growth' => $ordersGrowth,
            'average_order_value' => $averageOrderValue,
            'aov_growth' => $aovGrowth, 
        ];
    }

    /**
     * Get revenue data for chart.
     */
    private function getRevenueChartData($startDate)
    {
        $orders = Order::where('payment_status', 'paid')
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
    private function getOrdersChartData($startDate)
    {
        $orders = Order::where('created_at', '>=', $startDate)
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
    private function getTopProducts($startDate)
    {
        // Get order items from orders in the period
        $orderItems = OrderItem::whereHas('order', function ($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate);
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
    private function getOrderStatusDistribution($startDate)
    {
        $orders = Order::where('created_at', '>=', $startDate)
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
    private function getRecentOrders()
    {
        return Order::with('customer')
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

    
}