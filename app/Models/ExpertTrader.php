<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertTrader extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'bio',
        'min_capital',
        'max_capital',
        'total_followers',
        'max_followers',
        'preset_followers',
        'duration',
        'active_trades',
        'total_trades',
        'win_rate',
        'total_profit',
        'roi_percentage',
        'risk_level',
        'status',
        'auto_distribute',
        'distribution_interval',
        'distribution_percentage',
    ];

    protected $casts = [
        'min_capital' => 'decimal:2',
        'max_capital' => 'decimal:2',
        'win_rate' => 'decimal:2',
        'total_profit' => 'decimal:2',
        'roi_percentage' => 'decimal:2',
        'distribution_percentage' => 'decimal:2',
    ];

    // Relationship with subscribers
    public function subscribers()
    {
        return $this->hasMany(ExpertTraderSubscriber::class);
    }

    // Active subscribers only
    public function activeSubscribers()
    {
        return $this->hasMany(ExpertTraderSubscriber::class)->where('status', 'active');
    }

    // Check if trader is at max capacity
    public function isAtCapacity()
    {
        return $this->total_followers >= $this->max_followers;
    }

    // Get available slots
    public function getAvailableSlots()
    {
        return max(0, $this->max_followers - $this->total_followers);
    }
}
