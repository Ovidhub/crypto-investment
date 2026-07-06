<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CryptoStakingPlan;
use App\Models\StakingSubscription;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessCryptoStakingRewards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notificationService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notificationService = app(NotificationService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settings = Settings::first();

        // Check global pause
        if ($settings->pause_all_staking_distributions === 'on') {
            Log::info('Staking distributions globally paused');
            return;
        }

        // Check trade mode
        if ($settings->trade_mode !== 'on') {
            Log::info('Trade mode is off, skipping staking distributions');
            return;
        }

        // Get active plans with distribution enabled
        $plans = CryptoStakingPlan::where('status', 'active')
            ->where('auto_distribute', 'on')
            ->where('distribution_paused', 'off')
            ->get();

        foreach ($plans as $plan) {
            try {
                $this->processPlanRewards($plan, $settings);
            } catch (\Exception $e) {
                Log::error("Error processing staking plan {$plan->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Process rewards for a specific plan
     */
    protected function processPlanRewards($plan, $settings)
    {
        $now = Carbon::now();

        // Determine interval in minutes
        $intervalMinutes = $this->getIntervalMinutes($plan->reward_interval);

        // Get active subscriptions
        $subscriptions = $plan->activeSubscriptions()
            ->where(function($query) use ($now, $intervalMinutes) {
                $query->whereNull('last_reward_at')
                    ->orWhereRaw('TIMESTAMPDIFF(MINUTE, last_reward_at, ?) >= ?', [$now, $intervalMinutes]);
            })
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $this->processSubscriptionReward($subscription, $plan, $settings, $now);
            } catch (\Exception $e) {
                Log::error("Error processing subscription {$subscription->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Process reward for a single subscription
     */
    protected function processSubscriptionReward($subscription, $plan, $settings, $now)
    {
        // Check if subscription has expired
        if ($subscription->hasExpired()) {
            $this->completeSubscription($subscription, $settings);
            return;
        }

        // Check weekend trading
        $canTradeNow = $now->isWeekday() || $settings->weekend_trade === 'on';
        if (!$canTradeNow) {
            Log::info("Skipping subscription {$subscription->id} due to weekend trading restrictions");
            return;
        }

        DB::beginTransaction();
        try {
            // Calculate reward based on frozen APR
            $totalStaked = $subscription->amount_staked + $subscription->compounded_amount;
            $dailyAprRate = $subscription->apr_at_subscription / 365 / 100;
            $reward = $totalStaked * $dailyAprRate;

            // Handle compounding
            if ($subscription->is_compound_enabled && $plan->compound_enabled === 'on') {
                // Add to compounded amount
                $subscription->compounded_amount += $reward;
                $subscription->current_rewards += $reward;
                
                // Increment plan total staked
                $plan->increment('current_total_staked', $reward);

                $transactionType = 'Crypto Staking Compound';
            } else {
                // Add to user balance and ROI
                $subscription->user->account_bal += $reward;
                $subscription->user->roi += $reward;
                $subscription->user->save();

                $subscription->current_rewards += $reward;

                $transactionType = 'Crypto Staking Reward';
            }

            // Update subscription
            $subscription->last_reward_at = $now;
            $subscription->save();

            // Create transaction
            Tp_Transaction::create([
                'user' => $subscription->user_id,
                'plan' => "Staking Reward: {$plan->name}",
                'amount' => $reward,
                'type' => $transactionType,
                'status' => 'Processed',
            ]);

            // Send notification
            $formattedAmount = $settings->currency . number_format($reward, 2);
            $message = $subscription->is_compound_enabled 
                ? "Your staking reward of {$formattedAmount} from {$plan->name} has been auto-compounded."
                : "You received {$formattedAmount} staking reward from {$plan->name}.";

            $this->notificationService->createNotification(
                $subscription->user_id,
                $message,
                "Staking Reward Received",
                'success',
                route('crypto-staking.subscriptions'),
                'coins',
                'bg-success/10'
            );

            DB::commit();

            Log::info("Distributed {$reward} reward to user {$subscription->user_id} for subscription {$subscription->id}");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Complete expired subscription
     */
    protected function completeSubscription($subscription, $settings)
    {
        DB::beginTransaction();
        try {
            $totalReturn = $subscription->amount_staked + $subscription->compounded_amount + $subscription->current_rewards;

            // Return funds to user
            $subscription->user->account_bal += $totalReturn;
            $subscription->user->save();

            // Update subscription status
            $subscription->status = 'completed';
            $subscription->save();

            // Decrement plan total staked
            $subscription->stakingPlan->decrement('current_total_staked', $subscription->amount_staked + $subscription->compounded_amount);

            // Create transaction
            Tp_Transaction::create([
                'user' => $subscription->user_id,
                'plan' => "Staking Completed: {$subscription->stakingPlan->name}",
                'amount' => $totalReturn,
                'type' => 'Crypto Staking Return',
                'status' => 'Processed',
            ]);

            // Send notification
            $formattedAmount = $settings->currency . number_format($totalReturn, 2);
            $this->notificationService->createNotification(
                $subscription->user_id,
                "Your staking subscription for {$subscription->stakingPlan->name} has matured. Total return: {$formattedAmount} credited to your account.",
                "Staking Completed",
                'success',
                route('crypto-staking.subscriptions'),
                'check-circle',
                'bg-success/10'
            );

            DB::commit();

            Log::info("Completed subscription {$subscription->id} with return {$totalReturn}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to complete subscription {$subscription->id}: " . $e->getMessage());
        }
    }

    /**
     * Convert reward interval to minutes
     */
    private function getIntervalMinutes($interval)
    {
        switch ($interval) {
            case "5 Minutes":
                return 5;
            case "30 Minutes":
                return 30;
            case "Hourly":
                return 60;
            case "Daily":
                return 24 * 60;
            case "Weekly":
                return 7 * 24 * 60;
            case "Monthly":
                return 30 * 24 * 60;
            default:
                return 24 * 60; // Default to daily
        }
    }
}
