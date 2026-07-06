<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StakingSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crypto_staking_plan_id',
        'amount_staked',
        'current_rewards',
        'compounded_amount',
        'start_date',
        'end_date',
        'apr_at_subscription',
        'status',
        'last_reward_at',
        'lock_type',
        'is_compound_enabled',
        'force_completed_by',
        'force_completed_at',
        'force_complete_reason',
    ];

    protected $casts = [
        'amount_staked' => 'decimal:2',
        'current_rewards' => 'decimal:2',
        'compounded_amount' => 'decimal:2',
        'apr_at_subscription' => 'decimal:2',
        'is_compound_enabled' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_reward_at' => 'datetime',
        'force_completed_at' => 'datetime',
    ];

    /**
     * Relationship: User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Staking Plan
     */
    public function stakingPlan()
    {
        return $this->belongsTo(CryptoStakingPlan::class, 'crypto_staking_plan_id');
    }

    /**
     * Relationship: Admin who force completed
     */
    public function forceCompletedBy()
    {
        return $this->belongsTo(Admin::class, 'force_completed_by');
    }

    /**
     * Get total staked (including compounded)
     */
    public function getTotalStakedAttribute()
    {
        return $this->amount_staked + $this->compounded_amount;
    }

    /**
     * Get total return (staked + rewards)
     */
    public function getTotalReturnAttribute()
    {
        return $this->totalStaked + $this->current_rewards;
    }

    /**
     * Check if subscription has expired
     */
    public function hasExpired()
    {
        return Carbon::now()->greaterThan($this->end_date);
    }

    /**
     * Check if can withdraw
     */
    public function canWithdraw()
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Flexible can always withdraw
        if ($this->lock_type === 'flexible') {
            return true;
        }

        // Locked can withdraw if expired
        return $this->hasExpired();
    }

    /**
     * Calculate early withdrawal penalty
     */
    public function calculatePenalty()
    {
        if ($this->lock_type === 'flexible' || $this->hasExpired()) {
            return 0;
        }

        $penaltyPercent = $this->stakingPlan->early_withdrawal_penalty ?? 10;
        return ($this->current_rewards * $penaltyPercent) / 100;
    }

    /**
     * Get effective APR (frozen rate)
     */
    public function getEffectiveApr()
    {
        return $this->apr_at_subscription;
    }

    /**
     * Check if was force completed by admin
     */
    public function wasForceCompleted()
    {
        return $this->status === 'force_completed' && $this->force_completed_by !== null;
    }

    /**
     * Get profit percentage
     */
    public function getProfitPercentageAttribute()
    {
        if ($this->amount_staked == 0) {
            return 0;
        }
        return (($this->current_rewards + $this->compounded_amount) / $this->amount_staked) * 100;
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute()
    {
        $today = Carbon::now();
        $endDate = Carbon::parse($this->end_date);
        
        if ($today->greaterThan($endDate)) {
            return 0;
        }
        
        return $today->diffInDays($endDate);
    }

    /**
     * Get total duration in days
     */
    public function getTotalDurationDaysAttribute()
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        return $startDate->diffInDays($endDate);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->totalDurationDays == 0) {
            return 100;
        }
        
        $elapsed = $this->totalDurationDays - $this->daysRemaining;
        return ($elapsed / $this->totalDurationDays) * 100;
    }
}
