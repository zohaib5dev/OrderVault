<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_email',
        'business_phone',
        'business_website',
        'business_address',
        'business_logo',
        'tax_rate',
        'registration_number',
        'currency',
        'plan_id',
        'stripe_account_id',
        'trial_ends_at',
        'commission_rate',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

 

    public function getTotalRevenueAttribute()
    {
        return $this->orders()
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public function getActiveProductsAttribute()
    {
        return $this->products()
            ->where('is_active', true)
            ->count();
    }

    public function calculateCommission($amount)
    {
        return ($amount * $this->commission_rate) / 100;
    }

    public function getDashboardStats()
    {
        return [
            'total_revenue' => $this->getTotalRevenueAttribute(),
            'total_orders' => $this->getTotalOrdersAttribute(),
            'active_products' => $this->getActiveProductsAttribute(),
            'pending_orders' => $this->orders()
                ->where('status', 'pending')
                ->count(),
            'today_orders' => $this->orders()
                ->whereDate('created_at', today())
                ->count(),
            'today_revenue' => $this->orders()
                ->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total_amount')
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('business_name', 'LIKE', "%{$search}%");
    }
}