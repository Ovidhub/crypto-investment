<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTrade extends Model
{
    use HasFactory;

    // Order type constants
    const ORDER_TYPE_MARKET = 'market';
    const ORDER_TYPE_LIMIT = 'limit';
    const ORDER_TYPE_STOP = 'stop';

    // Expiry type constants
    const EXPIRY_GTC = 'gtc';        // Good Till Cancelled
    const EXPIRY_DAY = 'day';        // Day Order
    const EXPIRY_DATE = 'date';      // Custom Date

    protected $fillable = [
        'user_id',
        'stock_symbol',
        'trade_type',
        'order_type',
        'quantity',
        'price',
        'target_price',
        'trigger_price',
        'total_amount',
        'fee_amount',
        'fee_percent',
        'net_amount',
        'status',
        'expiry_type',
        'expires_at',
        'cancellation_reason',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'price' => 'decimal:4',
        'target_price' => 'decimal:4',
        'trigger_price' => 'decimal:4',
        'total_amount' => 'decimal:4',
        'fee_amount' => 'decimal:4',
        'fee_percent' => 'decimal:2',
        'net_amount' => 'decimal:4',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the trade
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the stock price information
     */
    public function stockPrice()
    {
        return $this->belongsTo(StockPrice::class, 'stock_symbol', 'symbol');
    }

    /**
     * Scope to get buy trades only
     */
    public function scopeBuys($query)
    {
        return $query->where('trade_type', 'buy');
    }

    /**
     * Scope to get sell trades only
     */
    public function scopeSells($query)
    {
        return $query->where('trade_type', 'sell');
    }

    /**
     * Scope to get completed trades
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get pending trades
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get active pending trades (not expired)
     */
    public function scopeActivePending($query)
    {
        return $query->where('status', 'pending')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Calculate fee amount based on percentage
     */
    public static function calculateFee($amount, $feePercent)
    {
        return round(($amount * $feePercent) / 100, 4);
    }

    /**
     * Calculate net amount (for buy: total + fee, for sell: total - fee)
     */
    public static function calculateNetAmount($totalAmount, $feeAmount, $tradeType)
    {
        if ($tradeType === 'buy') {
            return round($totalAmount + $feeAmount, 4);
        } else {
            return round($totalAmount - $feeAmount, 4);
        }
    }
}
