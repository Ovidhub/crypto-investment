<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoTrade extends Model
{
    protected $fillable = [
        'user_id',
        'coin_symbol',
        'trade_type',
        'quantity',
        'price_usd',
        'total_amount',
        'fee_amount',
        'fee_percent',
        'net_amount',
        'status',
        'order_type',
        'limit_price',
        'stop_price',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'price_usd' => 'decimal:8',
        'total_amount' => 'decimal:8',
        'fee_amount' => 'decimal:8',
        'fee_percent' => 'decimal:2',
        'net_amount' => 'decimal:8',
        'limit_price' => 'decimal:8',
        'stop_price' => 'decimal:8',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the trade
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the crypto price data
     */
    public function cryptoPrice(): BelongsTo
    {
        return $this->belongsTo(CryptoPrice::class, 'coin_symbol', 'coin_symbol');
    }

    /**
     * Check if trade is a buy
     */
    public function isBuy(): bool
    {
        return $this->trade_type === 'buy';
    }

    /**
     * Check if trade is a sell
     */
    public function isSell(): bool
    {
        return $this->trade_type === 'sell';
    }

    /**
     * Check if trade is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if trade is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if trade is market order
     */
    public function isMarketOrder(): bool
    {
        return $this->order_type === 'market';
    }

    /**
     * Check if trade is limit order
     */
    public function isLimitOrder(): bool
    {
        return $this->order_type === 'limit';
    }

    /**
     * Check if trade is stop-loss order
     */
    public function isStopLossOrder(): bool
    {
        return $this->order_type === 'stop_loss';
    }

    /**
     * Scope for completed trades
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending trades
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for buy trades
     */
    public function scopeBuys($query)
    {
        return $query->where('trade_type', 'buy');
    }

    /**
     * Scope for sell trades
     */
    public function scopeSells($query)
    {
        return $query->where('trade_type', 'sell');
    }
}
