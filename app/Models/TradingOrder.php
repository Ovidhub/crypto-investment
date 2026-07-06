<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_type',
        'side',
        'symbol',
        'market_type',
        'status',
        'quantity',
        'price',
        'stop_price',
        'filled_quantity',
        'average_price',
        'total_cost',
        'fee',
        'fee_currency',
        'filled_at',
        'cancelled_at',
        'notes',
        'external_order_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'stop_price' => 'decimal:8',
        'filled_quantity' => 'decimal:8',
        'average_price' => 'decimal:8',
        'total_cost' => 'decimal:8',
        'fee' => 'decimal:8',
        'filled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include open orders.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'partially_filled']);
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['filled', 'cancelled']);
    }

    /**
     * Determine if the order can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'partially_filled']);
    }

    /**
     * Cancel the order.
     */
    public function cancel()
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->save();

        return true;
    }

    /**
     * Calculate total value based on price and quantity.
     */
    public function getTotalValueAttribute()
    {
        if ($this->price && $this->quantity) {
            return $this->price * $this->quantity;
        }

        return null;
    }

    /**
     * Calculate remaining quantity.
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->filled_quantity;
    }
} 