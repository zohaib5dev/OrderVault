
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;


use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProfileController;

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Orders

    Route::post('orders/bulk-status', [OrderController::class, 'bulkUpdateStatus']);
    Route::get('orders/export', [OrderController::class, 'export']);

    // Products
    Route::get('products/filter', [ProductController::class, 'filter']);



   



    // Admin Panel
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index']);
    Route::apiResource('admin/customers', AdminCustomerController::class); 
    Route::get('admin/orders/{id}/download', [AdminOrderController::class, 'downloadInvoice']);
    Route::apiResource('admin/orders', AdminOrderController::class);
 

    Route::apiResource('admin/products', AdminProductController::class);
    Route::get('admin/categories/active', [CategoryController::class, 'getActive']);
    Route::apiResource('admin/categories', CategoryController::class);
    Route::get('admin/analytics', [AnalyticsController::class, 'analytics']);
 
    Route::get('admin/settings', [SettingsController::class, 'index']);
    Route::post('admin/settings', [SettingsController::class, 'store']);
    Route::put('admin/settings/{key}', [SettingsController::class, 'update']);
    Route::patch('admin/settings/{key}/toggle', [SettingsController::class, 'toggle']);
    Route::delete('admin/settings/{key}', [SettingsController::class, 'destroy']);
    Route::post('admin/settings/logo', [SettingsController::class, 'uploadLogo']);


    Route::get('customer/dashboard', [CustomerController::class, 'dashboard']);
    Route::get('customer/orders/{id}', [CustomerController::class, 'getOrder']);
    Route::get('customer/orders/{id}/download', [CustomerController::class, 'downloadInvoice']);
    Route::post('customer/orders/{id}/pay', [CustomerController::class, 'payInvoice']);


      Route::get('user/profile', [ProfileController::class, 'show']);
    Route::put('user/profile', [ProfileController::class, 'update']);
    Route::post('user/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::post('user/profile/password', [ProfileController::class, 'updatePassword']);


});

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('register', [AuthController::class, 'register']);
