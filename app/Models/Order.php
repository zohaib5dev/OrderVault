<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'vendor_id',
        'customer_id',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'billing_address',
        'subtotal',
        'tax_amount',
        'due_at',
        'shipping_cost',
        'discount_amount',
        'commission_amount',
        'total_amount',
        'currency',
        'status',
        'payment_status',
        'fulfillment_status',
        'payment_method',
        'stripe_payment_intent_id',
        'stripe_subscription_id',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'notes',
        'metadata',
        'shipping_method',
        'tracking_number',
        'discount_code_id'
    ];

    protected $casts = [
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(8));
        });
    }

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

  
    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            'shipped' => 'primary',
            'delivered' => 'success'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'secondary',
            'partially_paid' => 'info'
        ];

        return $badges[$this->payment_status] ?? 'secondary';
    }

    public function getFulfillmentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];

        return $badges[$this->fulfillment_status] ?? 'secondary';
    }

    public function getItemCountAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getUniqueItemsAttribute()
    {
        return $this->items->count();
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getShippingAddressFormattedAttribute()
    {
        return nl2br($this->shipping_address);
    }

    public function getBillingAddressFormattedAttribute()
    {
        return nl2br($this->billing_address);
    }

    // Methods
    public function calculateTotals()
    {
        $subtotal = $this->items->sum('total_price');
        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount;
        $this->save();

        return $this;
    }

    public function calculateCommission()
    {
        if ($this->vendor) {
            $commission = $this->vendor->calculateCommission($this->total_amount);
            $this->commission_amount = $commission;
            $this->save();
        }
        return $this;
    }

   

    public function canCancel()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canRefund()
    {
        return $this->payment_status === 'paid' && in_array($this->status, ['completed', 'shipped', 'delivered']);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('order_number', 'LIKE', "%{$search}%")
            ->orWhere('customer_email', 'LIKE', "%{$search}%")
            ->orWhere('customer_phone', 'LIKE', "%{$search}%");
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }
}