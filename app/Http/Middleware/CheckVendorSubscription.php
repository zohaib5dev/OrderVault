<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user || !$user->vendor) {
            return response()->json(['error' => 'Vendor not found'], 404);
        }

        $vendor = $user->vendor;
        
        // Check if vendor is active
        if ($vendor->is_active !== true) {
            return response()->json([
                'error' => 'Your account is not active',
                'status' => $vendor->is_active
            ], 403);
        }

        // Check subscription status
        $isValidSubscription = $this->checkSubscriptionValidity($vendor);
        
        if (!$isValidSubscription) {
            return response()->json([
                'error' => 'Your subscription has expired or is inactive',
                'subscription_status' => $vendor->subscription_status,
                'trial_ends_at' => $vendor->trial_ends_at,
                'subscription_ends_at' => $vendor->subscription_ends_at
            ], 403);
        }

        return $next($request);
    }


    private function checkSubscriptionValidity($vendor): bool
    {
        // If status is active
        if ($vendor->subscription_status === 'active') {
            if ($vendor->subscription_ends_at) {
                return now()->lessThan($vendor->subscription_ends_at);
            }
            return true; 
        }

        // If status is trial
        if ($vendor->subscription_status === 'trial') {
            if ($vendor->trial_ends_at) {
                return now()->lessThan($vendor->trial_ends_at);
            }
            return true; 
        }
        return false;
    }

}