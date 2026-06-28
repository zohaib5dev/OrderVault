<?php

use App\Http\Controllers\Api\Vendor\WebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Vendor\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');


Route::get('/stripe/refresh', [SubscriptionController::class, 'refreshStripeOnboarding'])->name('stripe.refresh');
Route::get('/stripe/return', [SubscriptionController::class, 'handleStripeReturn'])->name('stripe.return');
Route::get('/stripe/success', [SubscriptionController::class, 'handleStripeSuccess'])->name('stripe.success');

Route::get('/stripe/handle-webhook', [WebhookController::class, 'handleStripeWebhook'])->name('stripe.handle-webhook');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
