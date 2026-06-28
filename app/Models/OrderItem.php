<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'quantity',
        'total_price',
        'attributes',
        'tax_amount',
        'discount_amount',
        'subtotal'
    ];

    protected $casts = [
        'attributes' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getFormattedUnitPriceAttribute()
    {
        return '$' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute()
    {
        return '$' . number_format($this->total_price, 2);
    }

    public function getAttributesArrayAttribute()
    {
        return $this->attributes ?? [];
    }

    // Methods
    public function calculateSubtotal()
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->total_price = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();

        return $this;
    }

    public function getTotalSales()
    {
        return $this->whereHas('order', function ($query) {
            $query->where('payment_status', 'paid');
        })->sum('quantity');
    }

    public function getTotalRevenue()
    {
        return $this->whereHas('order', function ($query) {
            $query->where('payment_status', 'paid');
        })->sum('total_price');
    }

    // Scopes
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopePaid($query)
    {
        return $query->whereHas('order', function ($query) {
            $query->where('payment_status', 'paid');
        });
    }
}