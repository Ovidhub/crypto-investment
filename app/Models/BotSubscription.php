<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trading_bot_id',
        'amount_invested',
        'current_profit',
        'manual_profit_adjustment',
        'start_date',
        'end_date',
        'duration',
        'status',
        'last_trade_at',
        'last_manual_update_at',
        'last_manual_update_by',
    ];

    protected $casts = [
        'amount_invested' => 'decimal:2',
        'current_profit' => 'decimal:2',
        'manual_profit_adjustment' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'last_trade_at' => 'datetime',
        'last_manual_update_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trading bot
     */
    public function tradingBot()
    {
        return $this->belongsTo(TradingBot::class, 'trading_bot_id');
    }

    /**
     * Get all trades for this subscription
     */
    public function trades()
    {
        return $this->hasMany(BotTrade::class, 'bot_subscription_id');
    }

    /**
     * Get the admin who made the last manual update
     */
    public function lastUpdatedBy()
    {
        return $this->belongsTo(Admin::class, 'last_manual_update_by');
    }

    /**
     * Get total return (invested + profit)
     */
    public function getTotalReturnAttribute()
    {
        return $this->amount_invested + $this->current_profit + ($this->manual_profit_adjustment ?? 0);
    }

    /**
     * Get profit percentage
     */
    public function getProfitPercentageAttribute()
    {
        if ($this->amount_invested <= 0) {
            return 0;
        }
        $totalProfit = $this->current_profit + ($this->manual_profit_adjustment ?? 0);
        return ($totalProfit / $this->amount_invested) * 100;
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired()
    {
        return $this->end_date && now()->isAfter($this->end_date);
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->end_date) {
            return null;
        }
        return max(0, now()->diffInDays($this->end_date, false));
    }

    /**
     * Check if enough time has passed for next trade
     */
    public function canTrade()
    {
        if (!$this->last_trade_at) {
            return true;
        }

        $intervalMinutes = $this->tradingBot->getIntervalMinutes();
        return now()->diffInMinutes($this->last_trade_at) >= $intervalMinutes;
    }
}
