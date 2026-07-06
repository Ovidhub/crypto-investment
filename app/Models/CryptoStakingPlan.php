<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoStakingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'coin_symbol',
        'name',
        'description',
        'photo',
        'apr_percentage',
        'min_stake',
        'max_stake',
        'max_total_staked',
        'current_total_staked',
        'lock_duration',
        'reward_interval',
        'early_withdrawal_penalty',
        'compound_enabled',
        'auto_distribute',
        'distribution_paused',
        'status',
    ];

    protected $casts = [
        'apr_percentage' => 'decimal:2',
        'min_stake' => 'decimal:2',
        'max_stake' => 'decimal:2',
        'max_total_staked' => 'decimal:2',
        'current_total_staked' => 'decimal:2',
        'early_withdrawal_penalty' => 'decimal:2',
    ];

    /**
     * Relationship: Crypto Price
     */
    public function cryptoPrice()
    {
        return $this->belongsTo(CryptoPrice::class, 'coin_symbol', 'coin_symbol');
    }

    /**
     * Relationship: All Subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(StakingSubscription::class, 'crypto_staking_plan_id');
    }

    /**
     * Relationship: Active Subscriptions
     */
    public function activeSubscriptions()
    {
        return $this->hasMany(StakingSubscription::class, 'crypto_staking_plan_id')
            ->where('status', 'active');
    }

    /**
     * Get daily APR rate
     */
    public function getAprDaily()
    {
        return $this->apr_percentage / 365;
    }

    /**
     * Check if plan is at capacity
     */
    public function isAtCapacity()
    {
        if (!$this->max_total_staked) {
            return false;
        }
        return $this->current_total_staked >= $this->max_total_staked;
    }

    /**
     * Check if adding amount would exceed capacity
     */
    public function hasAvailableCapacity($amount)
    {
        if (!$this->max_total_staked) {
            return true;
        }
        return ($this->current_total_staked + $amount) <= $this->max_total_staked;
    }

    /**
     * Get recommended APR based on crypto type
     */
    public function getRecommendedAprAttribute()
    {
        $symbol = strtoupper($this->coin_symbol);
        
        // Stablecoins
        if (in_array($symbol, ['USDT', 'USDC', 'DAI', 'USDS', 'USDE', 'PYUSD', 'USD1'])) {
            return '10-12%';
        }
        
        // Major coins (BTC, ETH)
        if (in_array($symbol, ['BTC', 'ETH', 'WBTC', 'WETH', 'STETH', 'WSTETH'])) {
            return '5-8%';
        }
        
        // Layer 1s and popular alts
        if (in_array($symbol, ['BNB', 'SOL', 'ADA', 'AVAX', 'DOT', 'MATIC', 'LINK'])) {
            return '10-15%';
        }
        
        // High-risk/smaller caps
        return '15-25%';
    }

    /**
     * Check if distributions can run
     */
    public function canDistribute()
    {
        if ($this->distribution_paused === 'on') {
            return false;
        }

        $settings = \App\Models\Settings::first();
        if ($settings && $settings->pause_all_staking_distributions === 'on') {
            return false;
        }

        return $this->auto_distribute === 'on';
    }

    /**
     * Get top stakers for this plan
     */
    public function topStakers($limit = 10)
    {
        return $this->subscriptions()
            ->with('user')
            ->selectRaw('user_id, SUM(amount_staked + compounded_amount) as total_staked, SUM(current_rewards) as total_rewards')
            ->groupBy('user_id')
            ->orderByDesc('total_rewards')
            ->limit($limit)
            ->get();
    }
}
