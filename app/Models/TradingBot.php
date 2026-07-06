<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'strategy_type',
        'photo',
        'win_rate',
        'roi_percentage',
        'total_trades',
        'total_profit',
        'min_capital',
        'max_capital',
        'auto_trade',
        'trade_interval',
        'min_profit',
        'max_profit',
        'min_loss',
        'max_loss',
        'status',
        'fake_active_users',
    ];

    protected $casts = [
        'win_rate' => 'decimal:2',
        'roi_percentage' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'min_capital' => 'decimal:2',
        'max_capital' => 'decimal:2',
        'min_profit' => 'decimal:2',
        'max_profit' => 'decimal:2',
        'min_loss' => 'decimal:2',
        'max_loss' => 'decimal:2',
    ];

    /**
     * Get all subscriptions for this bot
     */
    public function subscriptions()
    {
        return $this->hasMany(BotSubscription::class, 'trading_bot_id');
    }

    /**
     * Get active subscriptions
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(BotSubscription::class, 'trading_bot_id')
            ->where('status', 'active');
    }

    /**
     * Get all trades for this bot
     */
    public function trades()
    {
        return $this->hasMany(BotTrade::class, 'trading_bot_id');
    }

    /**
     * Get total subscribers count
     */
    public function getTotalSubscribersAttribute()
    {
        return $this->subscriptions()->count();
    }

    /**
     * Get active subscribers count
     */
    public function getActiveSubscribersAttribute()
    {
        return $this->activeSubscriptions()->count();
    }

    /**
     * Check if bot is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if auto trading is enabled
     */
    public function hasAutoTrading()
    {
        return $this->auto_trade === 'on';
    }

    /**
     * Get trade interval in minutes
     */
    public function getIntervalMinutes()
    {
        switch ($this->trade_interval) {
            case '5 Minutes':
                return 5;
            case '30 Minutes':
                return 30;
            case 'Hourly':
                return 60;
            case 'Daily':
                return 1440;
            default:
                return 5;
        }
    }
}
