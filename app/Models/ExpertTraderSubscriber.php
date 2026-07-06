<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertTraderSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_trader_id',
        'user_id',
        'amount_invested',
        'start_date',
        'end_date',
        'current_profit',
        'status',
        'last_profit_added',
    ];

    protected $casts = [
        'amount_invested' => 'decimal:2',
        'current_profit' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_profit_added' => 'datetime',
    ];

    // Relationship with expert trader
    public function expertTrader()
    {
        return $this->belongsTo(ExpertTrader::class);
    }

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate total return (investment + profit)
    public function getTotalReturn()
    {
        return $this->amount_invested + $this->current_profit;
    }

    // Calculate profit percentage
    public function getProfitPercentage()
    {
        if ($this->amount_invested == 0) {
            return 0;
        }
        return ($this->current_profit / $this->amount_invested) * 100;
    }

    // Check if subscription is active
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= now();
    }
}
