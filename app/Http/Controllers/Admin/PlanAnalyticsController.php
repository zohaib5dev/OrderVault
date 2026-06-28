<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->input('days', 30);
        $planId = $request->input('plan_id');
        
        $startDate = Carbon::now()->subDays($days);
        $previousStartDate = Carbon::now()->subDays($days * 2);
        
        // Get all plans for filter
        $plans = Plan::where('status', 'active')->get();
        
        // Current period metrics
        $currentMetrics = $this->getMetrics($startDate, Carbon::now(), $planId);
        
        // Previous period metrics for growth
        $previousMetrics = $this->getMetrics($previousStartDate, $startDate, $planId);
        
        // Calculate metrics
        $metrics = [
            'total_revenue' => $currentMetrics['revenue'],
            'total_purchases' => $currentMetrics['purchases'],
            'active_subscriptions' => $this->getActiveSubscriptions($planId),
            'average_price' => $this->getAveragePrice($startDate, $planId),
            'subscription_revenue' => $currentMetrics['subscription_revenue'],
            'one_time_revenue' => $currentMetrics['one_time_revenue'],
            'subscription_percentage' => $currentMetrics['revenue'] > 0 
                ? round(($currentMetrics['subscription_revenue'] / $currentMetrics['revenue']) * 100, 2)
                : 0,
            'one_time_percentage' => $currentMetrics['revenue'] > 0 
                ? round(($currentMetrics['one_time_revenue'] / $currentMetrics['revenue']) * 100, 2)
                : 0,
            'revenue_growth' => $this->calculateGrowth($currentMetrics['revenue'], $previousMetrics['revenue']),
            'purchases_growth' => $this->calculateGrowth($currentMetrics['purchases'], $previousMetrics['purchases']),
            'subscriptions_growth' => $this->calculateSubscriptionGrowth($startDate, $planId),
           // 'price_growth' => $this->calculateGrowth($metrics['average_price'], $this->getAveragePrice($previousStartDate, $planId)),
        ];
        
        // Revenue data for chart
        $revenueData = $this->getRevenueData($days, $planId);
        
        // Purchases data for chart
        $purchasesData = $this->getPurchasesData($days, $planId);
        
        // Top plans
        $topPlans = $this->getTopPlans($startDate, $planId);
        
        // Plan type distribution
        $planTypeDistribution = $this->getPlanTypeDistribution($planId);
        
        // Subscription status
        $subscriptionStatus = $this->getSubscriptionStatus($planId);
        
        // Recent purchases
        $recentPurchases = $this->getRecentPurchases($planId);
        
        // Plan performance
        $planPerformance = $this->getPlanPerformance($startDate, $planId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'metrics' => $metrics,
                'revenue_data' => $revenueData,
                'purchases_data' => $purchasesData,
                'top_plans' => $topPlans,
                'plan_type_distribution' => $planTypeDistribution,
                'subscription_status' => $subscriptionStatus,
                'recent_purchases' => $recentPurchases,
                'plan_performance' => $planPerformance,
            ],
            'plans' => $plans
        ]);
    }
    
    private function getMetrics($startDate, $endDate, $planId = null)
    {
        $query = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->when($planId, function($q) use ($planId) {
                return $q->where('plan_id', $planId);
            });
            
        $subscriptionRevenue = $query->clone()
            ->where('type', 'subscription')
            ->sum('amount');
            
        $oneTimeRevenue = $query->clone()
            ->where('type', 'one_time')
            ->sum('amount');
            
        $totalRevenue = $subscriptionRevenue + $oneTimeRevenue;
        $totalPurchases = $query->count();
        
        return [
            'revenue' => $totalRevenue,
            'purchases' => $totalPurchases,
            'subscription_revenue' => $subscriptionRevenue,
            'one_time_revenue' => $oneTimeRevenue,
        ];
    }
    
    private function getActiveSubscriptions($planId = null)
    {
        $query = Subscription::where('status', 'active');
        
        if ($planId) {
            $query->where('plan_id', $planId);
        }
        
        return $query->count();
    }
    
    private function getAveragePrice($startDate, $planId = null)
    {
        $query = Invoice::where('created_at', '>=', $startDate);
        
        if ($planId) {
            $query->where('plan_id', $planId);
        }
        
        $avg = $query->avg('amount');
        return $avg ? round($avg, 2) : 0;
    }
    
    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 2);
    }
    
    private function calculateSubscriptionGrowth($startDate, $planId = null)
    {
        $previousStartDate = Carbon::now()->subDays($startDate->diffInDays(Carbon::now()) * 2);
        
        $current = $this->getActiveSubscriptions($planId);
        $previous = Subscription::where('status', 'active')
            ->where('created_at', '>=', $previousStartDate)
            ->where('created_at', '<', $startDate)
            ->when($planId, function($q) use ($planId) {
                return $q->where('plan_id', $planId);
            })
            ->count();
            
        return $this->calculateGrowth($current, $previous);
    }
    
    private function getRevenueData($days, $planId = null)
    {
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $subscriptionRevenue = Invoice::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('type', 'subscription')
                ->when($planId, function($q) use ($planId) {
                    return $q->where('plan_id', $planId);
                })
                ->sum('amount');
                
            $oneTimeRevenue = Invoice::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('type', 'one_time')
                ->when($planId, function($q) use ($planId) {
                    return $q->where('plan_id', $planId);
                })
                ->sum('amount');
                
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'subscription_revenue' => $subscriptionRevenue,
                'one_time_revenue' => $oneTimeRevenue,
                'total_revenue' => $subscriptionRevenue + $oneTimeRevenue,
            ];
        }
        
        return $data;
    }
    
    private function getPurchasesData($days, $planId = null)
    {
        $data = [];
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $subscriptions = Invoice::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('type', 'subscription')
                ->when($planId, function($q) use ($planId) {
                    return $q->where('plan_id', $planId);
                })
                ->count();
                
            $oneTime = Invoice::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('type', 'one_time')
                ->when($planId, function($q) use ($planId) {
                    return $q->where('plan_id', $planId);
                })
                ->count();
                
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'subscriptions' => $subscriptions,
                'one_time' => $oneTime,
                'total' => $subscriptions + $oneTime,
            ];
        }
        
        return $data;
    }
    
    private function getTopPlans($startDate, $planId = null)
    {
        $query = Plan::where('status', 'active');
        
        if ($planId) {
            $query->where('id', $planId);
        }
        
        $plans = $query->get();
        $planData = [];
        $maxRevenue = 0;
        
        foreach ($plans as $plan) {
            $purchases = Invoice::where('plan_id', $plan->id)
                ->where('created_at', '>=', $startDate)
                ->get();
                
            $revenue = $purchases->sum('amount');
            $subscriptions = $purchases->where('type', 'subscription')->count();
            $oneTimePurchases = $purchases->where('type', 'one_time')->count();
            
            if ($revenue > 0) {
                $planData[] = [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'revenue' => $revenue,
                    'purchases' => $purchases->count(),
                    'subscriptions' => $subscriptions,
                    'one_time_purchases' => $oneTimePurchases,
                    'type' => $plan->type,
                    'percentage' => 0,
                ];
                
                if ($revenue > $maxRevenue) {
                    $maxRevenue = $revenue;
                }
            }
        }
        
        // Calculate percentages and sort
        foreach ($planData as &$plan) {
            $plan['percentage'] = $maxRevenue > 0 
                ? round(($plan['revenue'] / $maxRevenue) * 100, 2) 
                : 0;
        }
        
        usort($planData, function($a, $b) {
            return $b['revenue'] - $a['revenue'];
        });
        
        return array_slice($planData, 0, 10);
    }
    
    private function getPlanTypeDistribution($planId = null)
    {
        $query = Plan::query();
        
        if ($planId) {
            $query->where('id', $planId);
        }
        
        $plans = $query->get();
        $total = $plans->count();
        
        $subscriptionCount = $plans->where('type', 'subscription')->count();
        $oneTimeCount = $plans->where('type', 'one_time')->count();
        
        return [
            [
                'type' => 'subscription',
                'label' => 'Subscription Plans',
                'count' => $subscriptionCount,
                'percentage' => $total > 0 ? round(($subscriptionCount / $total) * 100, 2) : 0,
                'color' => '#8B5CF6'
            ],
            [
                'type' => 'one_time',
                'label' => 'One-time Plans',
                'count' => $oneTimeCount,
                'percentage' => $total > 0 ? round(($oneTimeCount / $total) * 100, 2) : 0,
                'color' => '#10B981'
            ]
        ];
    }
    
    private function getSubscriptionStatus($planId = null)
    {
        $query = Subscription::query();
        
        if ($planId) {
            $query->where('plan_id', $planId);
        }
        
        $statuses = $query->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
            
        $total = $statuses->sum('count');
        $colors = [
            'active' => '#10B981',
            'pending' => '#F59E0B',
            'cancelled' => '#EF4444',
            'expired' => '#6B7280'
        ];
        $labels = [
            'active' => 'Active',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired'
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
    
    private function getRecentPurchases($planId = null)
    {
        $query = Invoice::with(['plan', 'vendor'])
            ->orderBy('created_at', 'desc')
            ->limit(10);
            
        if ($planId) {
            $query->where('plan_id', $planId);
        }
        
        return $query->get()->map(function($purchase) {
            return [
                'id' => $purchase->id,
                'vendor_name' => $purchase->vendor->business_name ?? 'Unknown Vendor',
                'plan_name' => $purchase->plan->name ?? 'Unknown Plan',
                'type' => $purchase->type,
                'amount' => $purchase->amount,
                'status' => $purchase->status,
                'created_at' => $purchase->created_at,
            ];
        })->toArray();
    }
    
    private function getPlanPerformance($startDate, $planId = null)
    {
        $query = Plan::query();
        
        if ($planId) {
            $query->where('id', $planId);
        }
        
        return $query->get()->map(function($plan) use ($startDate) {
            $purchases = Invoice::where('plan_id', $plan->id)
                ->where('created_at', '>=', $startDate)
                ->get();
                
            $subscriptions = Subscription::where('plan_id', $plan->id)
                ->where('status', 'active')
                ->count();
                
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
                'type' => $plan->type,
                'total_purchases' => $purchases->count(),
                'total_revenue' => $purchases->sum('amount'),
                'active_subscriptions' => $subscriptions,
                'status' => $plan->status,
                'created_at' => $plan->created_at,
            ];
        })->toArray();
    }
}