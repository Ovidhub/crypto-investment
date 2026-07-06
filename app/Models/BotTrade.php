<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'bot_subscription_id',
        'trading_bot_id',
        'user_id',
        'asset_symbol',
        'asset_name',
        'asset_logo',
        'entry_price',
        'exit_price',
        'amount',
        'profit_loss',
        'profit_percentage',
        'outcome',
        'trade_type',
        'created_by_admin_id',
        'notes',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'entry_price' => 'decimal:8',
        'exit_price' => 'decimal:8',
        'amount' => 'decimal:2',
        'profit_loss' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the subscription
     */
    public function subscription()
    {
        return $this->belongsTo(BotSubscription::class, 'bot_subscription_id');
    }

    /**
     * Get the trading bot
     */
    public function tradingBot()
    {
        return $this->belongsTo(TradingBot::class, 'trading_bot_id');
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who created this trade (for manual trades)
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Check if trade was a win
     */
    public function isWin()
    {
        return $this->outcome === 'win';
    }

    /**
     * Check if trade was a loss
     */
    public function isLoss()
    {
        return $this->outcome === 'loss';
    }

    /**
     * Check if trade was automated
     */
    public function isAutomated()
    {
        return $this->trade_type === 'automated';
    }

    /**
     * Check if trade was manual
     */
    public function isManual()
    {
        return $this->trade_type === 'manual';
    }

    /**
     * Get trade duration in hours
     */
    public function getDurationAttribute()
    {
        if (!$this->opened_at || !$this->closed_at) {
            return null;
        }
        return $this->opened_at->diffInHours($this->closed_at);
    }

    /**
     * Scope for wins only
     */
    public function scopeWins($query)
    {
        return $query->where('outcome', 'win');
    }

    /**
     * Scope for losses only
     */
    public function scopeLosses($query)
    {
        return $query->where('outcome', 'loss');
    }

    /**
     * Scope for automated trades
     */
    public function scopeAutomated($query)
    {
        return $query->where('trade_type', 'automated');
    }

    /**
     * Scope for manual trades
     */
    public function scopeManual($query)
    {
        return $query->where('trade_type', 'manual');
    }
}
