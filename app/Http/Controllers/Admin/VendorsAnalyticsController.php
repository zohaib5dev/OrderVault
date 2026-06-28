<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorsAnalyticsController extends Controller
{
    public function index(Request $request)
    {

        $days = $request->input('days', 30);
        $vendorId = $request->input('vendor_id');
        
        $startDate = Carbon::now()->subDays($days);
        $previousStartDate = Carbon::now()->subDays($days * 2);
        
        // Base query for orders
        $ordersQuery = Order::query()
            ->when($vendorId, function($query) use ($vendorId) {
                return $query->where('vendor_id', $vendorId);
            });
        
        // Current period metrics
        $currentMetrics = $this->getMetrics($ordersQuery, $startDate, Carbon::now());
        
        // Previous period metrics for growth
        $previousMetrics = $this->getMetrics(
            clone $ordersQuery,
            $previousStartDate,
            $startDate
        );
        
        // Calculate growth percentages
        $metrics = [
            'total_revenue' => $currentMetrics['revenue'],
            'total_orders' => $currentMetrics['orders'],
            'active_vendors' => $this->getActiveVendors($startDate, $vendorId),
            'average_order_value' => $currentMetrics['orders'] > 0 
                ? round($currentMetrics['revenue'] / $currentMetrics['orders'], 2) 
                : 0,
            'conversion_rate' => $this->getConversionRate($startDate, $vendorId),
            'revenue_growth' => $this->calculateGrowth($currentMetrics['revenue'], $previousMetrics['revenue']),
            'orders_growth' => $this->calculateGrowth($currentMetrics['orders'], $previousMetrics['orders']),
            'vendors_growth' => $this->calculateVendorGrowth($startDate, $vendorId),
            'conversion_growth' => $this->calculateConversionGrowth($startDate, $vendorId),
        ];
        
        // Revenue data for chart
        $revenueData = $this->getRevenueData($days, $vendorId);
        
        // Orders data for chart
        $ordersData = $this->getOrdersData($days, $vendorId);
        
        // Top vendors
        $topVendors = $this->getTopVendors($startDate, $vendorId);
        
        // Order status distribution
        $orderStatusDistribution = $this->getOrderStatusDistribution($startDate, $vendorId);
        
        // Vendor performance summary
        $vendorPerformance = $this->getVendorPerformance($startDate, $vendorId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => $metrics,
                'revenue_data' => $revenueData,
                'orders_data' => $ordersData,
                'top_vendors' => $topVendors,
                'order_status_distribution' => $orderStatusDistribution,
                'vendor_performance' => $vendorPerformance,
            ]
        ]);
    }
    
   private function getMetrics($query, $startDate, $endDate)
{
    $filteredQuery = (clone $query)
        ->whereBetween('created_at', [$startDate, $endDate]);

    return [
        'revenue' => $filteredQuery->sum('total_amount'),
        'orders' => $filteredQuery->count(),
    ];
}
    
    private function getActiveVendors($startDate, $vendorId = null)
    {
        if ($vendorId) {
            return Vendor::where('id', $vendorId)
                ->where('status', 'active')
                ->count();
        }
        
        return Vendor::where('status', 'active')
            ->whereHas('orders', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->count();
    }
    
    private function getConversionRate($startDate, $vendorId = null)
    {
        $totalVisitors = $this->getTotalVisitors($startDate, $vendorId);
        $totalOrders = Order::where('created_at', '>=', $startDate)
            ->when($vendorId, function($query) use ($vendorId) {
                return $query->where('vendor_id', $vendorId);
            })
            ->count();
            
        return $totalVisitors > 0 
            ? round(($totalOrders / $totalVisitors) * 100, 2) 
            : 0;
    }
    
    private function getTotalVisitors($startDate, $vendorId = null)
    {
        // Implement your visitor tracking logic here
        // This is a placeholder - you might track visitors differently
        return 1000;
    }
    
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }
    
    private function calculateVendorGrowth($startDate, $vendorId = null)
    {
        $previousStartDate = Carbon::now()->subDays($startDate->diffInDays(Carbon::now()) * 2);
        
        $currentVendors = $this->getActiveVendors($startDate, $vendorId);
        $previousVendors = $this->getActiveVendors($previousStartDate, $vendorId);
        
        return $this->calculateGrowth($currentVendors, $previousVendors);
    }
    
    private function calculateConversionGrowth($startDate, $vendorId = null)
    {
        $previousStartDate = Carbon::now()->subDays($startDate->diffInDays(Carbon::now()) * 2);
        
        $currentRate = $this->getConversionRate($startDate, $vendorId);
        $previousRate = $this->getConversionRate($previousStartDate, $vendorId);
        
        return $this->calculateGrowth($currentRate, $previousRate);
    }
    
    private function getRevenueData($days, $vendorId = null)
    {
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->when($vendorId, function($query) use ($vendorId) {
                    return $query->where('vendor_id', $vendorId);
                })
                ->sum('total_amount');
                
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => $revenue
            ];
        }
        
        return $data;
    }
    
    private function getOrdersData($days, $vendorId = null)
    {
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $orders = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->when($vendorId, function($query) use ($vendorId) {
                    return $query->where('vendor_id', $vendorId);
                })
                ->count();
                
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'orders' => $orders
            ];
        }
        
        return $data;
    }
    
    private function getTopVendors($startDate, $vendorId = null)
    {
        if ($vendorId) {
            $vendor = Vendor::find($vendorId);
            if ($vendor) {
                $revenue = Order::where('vendor_id', $vendorId)
                    ->where('created_at', '>=', $startDate)
                    ->sum('total_amount');
                    
                return [[
                    'id' => $vendor->id,
                    'name' => $vendor->business_name,
                    'revenue' => $revenue,
                    'percentage' => 100
                ]];
            }
            return [];
        }
        
        $topVendors = Vendor::withCount(['orders' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->withSum(['orders as total_revenue' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }], 'total_amount')
            ->where('status', 'active')
            //->having('total_revenue', '>', 0)
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();
            
        $maxRevenue = $topVendors->max('total_revenue') ?? 0;
        
        return $topVendors->map(function($vendor) use ($maxRevenue) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'revenue' => $vendor->total_revenue ?? 0,
                'percentage' => $maxRevenue > 0 
                    ? round(($vendor->total_revenue / $maxRevenue) * 100, 2) 
                    : 0
            ];
        })->toArray();
    }
    
    private function getOrderStatusDistribution($startDate, $vendorId = null)
    {
     $statuses = Order::where('created_at', '>=', $startDate)
    ->when($vendorId, function ($query) use ($vendorId) {
        $query->where('vendor_id', $vendorId);
    })
    ->select('status')
    ->groupBy('status')
    ->selectRaw('count(*) as count')
    ->get();
            
        $total = $statuses->sum('count');
        $colors = [
            'pending' => '#F59E0B',
            'processing' => '#3B82F6',
            'shipped' => '#8B5CF6',
            'delivered' => '#10B981',
            'completed' => '#059669',
            'cancelled' => '#EF4444'
        ];
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
        
        return $statuses->map(function($status) use ($total, $colors, $labels) {
            return [
                'status' => $status->status,
                'label' => $labels[$status->status] ?? $status->status,
                'count' => $status->count,
                'percentage' => $total > 0 ? round(($status->count / $total) * 100, 2) : 0,
                'color' => $colors[$status->status] ?? '#6B7280'
            ];
        })->toArray();
    }
    
    private function getVendorPerformance($startDate, $vendorId = null)
    {
        $vendorsQuery = Vendor::where('status', 'active');
        
        if ($vendorId) {
            $vendorsQuery->where('id', $vendorId);
        }
        
        return $vendorsQuery->get()->map(function($vendor) use ($startDate) {
            $orders = Order::where('vendor_id', $vendor->id)
                ->where('created_at', '>=', $startDate)
                ->get();
                
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'total_orders' => $orders->count(),
                'total_revenue' => $orders->sum('total_amount'),
                'average_order_value' => $orders->count() > 0 
                    ? round($orders->sum('total_amount') / $orders->count(), 2) 
                    : 0,
                'status' => $vendor->status,
                'created_at' => $vendor->created_at
            ];
        })->toArray();
    }
}