<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CryptoStakingPlan;
use App\Models\StakingSubscription;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use App\Mail\NewNotification;
use App\Notifications\InvestmentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CryptoStakingController extends Controller
{
    /**
     * Display available staking plans
     */
    public function index()
    {
        $plans = CryptoStakingPlan::with('cryptoPrice')
            ->where('status', 'active')
            ->orderBy('apr_percentage', 'desc')
            ->get();
        
        $settings = Settings::first();
        $title = 'Crypto Staking';
        
        return view('user.crypto-staking.index', compact('plans', 'settings', 'title'));
    }

    /**
     * Show single staking plan details
     */
    public function show($id)
    {
        $plan = CryptoStakingPlan::with('cryptoPrice')->findOrFail($id);
        $settings = Settings::first();
        $user = Auth::user();
        $title = 'Stake ' . $plan->name;
        
        // Check if user already has active subscription for this plan
        $hasActiveSubscription = StakingSubscription::where('user_id', $user->id)
            ->where('crypto_staking_plan_id', $plan->id)
            ->where('status', 'active')
            ->exists();
        
        // Calculate max days based on lock_duration
        $maxDays = 365; // Default to 1 year
        if ($plan->lock_duration != 'Flexible') {
            // Parse lock_duration (e.g., "30 days", "90 days", "1 year")
            if (preg_match('/(\d+)\s*day/i', $plan->lock_duration, $matches)) {
                $maxDays = (int)$matches[1];
            } elseif (preg_match('/(\d+)\s*month/i', $plan->lock_duration, $matches)) {
                $maxDays = (int)$matches[1] * 30;
            } elseif (preg_match('/(\d+)\s*year/i', $plan->lock_duration, $matches)) {
                $maxDays = (int)$matches[1] * 365;
            }
        }
        
        return view('user.crypto-staking.show', compact('plan', 'settings', 'user', 'title', 'hasActiveSubscription', 'maxDays'));
    }

    /**
     * Subscribe to a staking plan
     */
    public function subscribe(Request $request, $id)
    {
        $plan = CryptoStakingPlan::with('cryptoPrice')->findOrFail($id);
        $user = Auth::user();
        $settings = Settings::first();

        // Validate request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $plan->min_stake,
            'enable_compound' => 'nullable|boolean',
        ]);

        // Check if plan is active
        if ($plan->status !== 'active') {
            return back()->with('error', 'This staking plan is currently not available.');
        }

        // Check if distributions are paused
        if ($plan->distribution_paused === 'on') {
            return back()->with('error', 'This staking plan has temporarily paused distributions. Please try another plan.');
        }

        // Validate amount
        $amount = $validated['amount'];
        if ($amount < $plan->min_stake) {
            return back()->with('error', "Minimum stake amount is {$settings->currency}" . number_format($plan->min_stake, 2));
        }

        if ($amount > $plan->max_stake) {
            return back()->with('error', "Maximum stake amount is {$settings->currency}" . number_format($plan->max_stake, 2));
        }

        // Check user balance
        if ($user->account_bal < $amount) {
            return back()->with('error', 'Insufficient balance. Please fund your account.');
        }

        // Check plan capacity
        if (!$plan->hasAvailableCapacity($amount)) {
            $available = $plan->max_total_staked - $plan->current_total_staked;
            return back()->with('error', "Plan capacity exceeded. Available: {$settings->currency}" . number_format($available, 2));
        }

        DB::beginTransaction();
        try {
            // Calculate end date
            $startDate = Carbon::now();
            $endDate = $this->calculateEndDate($startDate, $plan->lock_duration);
            $lockType = $plan->lock_duration === 'Flexible' ? 'flexible' : 'locked';

            // Create subscription
            $subscription = StakingSubscription::create([
                'user_id' => $user->id,
                'crypto_staking_plan_id' => $plan->id,
                'amount_staked' => $amount,
                'current_rewards' => 0,
                'compounded_amount' => 0,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'apr_at_subscription' => $plan->apr_percentage, // Freeze APR at subscription time
                'status' => 'active',
                'last_reward_at' => $startDate,
                'lock_type' => $lockType,
                'is_compound_enabled' => $request->input('enable_compound', false) && $plan->compound_enabled === 'on',
            ]);

            // Deduct from user balance
            $user->account_bal -= $amount;
            $user->save();

            // Update plan total staked
            $plan->increment('current_total_staked', $amount);

            // Create transaction record
            Tp_Transaction::create([
                'user' => $user->id,
                'plan' => "Crypto Staking: {$plan->name}",
                'amount' => $amount,
                'type' => 'Crypto Staking Subscription',
                'status' => 'Processed',
            ]);

            // Send email notification
            $message = "You have successfully staked {$settings->currency}" . number_format($amount, 2) . 
                      " in {$plan->name}. APR: {$plan->apr_percentage}%. Duration: {$plan->lock_duration}. " .
                      "Rewards will be distributed {$plan->reward_interval}.";
            
            Mail::to($user->email)->send(new NewNotification(
                $message,
                'Crypto Staking Subscription Successful',
                $user->name
            ));

            DB::commit();

            return redirect()->route('crypto-staking.subscriptions')
                ->with('success', 'Successfully staked! You will start earning rewards automatically.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staking subscription error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * View user's staking subscriptions
     */
    public function mySubscriptions()
    {
        $user = Auth::user();
        $subscriptions = StakingSubscription::with(['stakingPlan.cryptoPrice'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $settings = Settings::first();
        $title = 'My Staking Subscriptions';

        // Calculate portfolio totals
        $totalStaked = $subscriptions->where('status', 'active')->sum(function($sub) {
            return $sub->amount_staked + $sub->compounded_amount;
        });
        $totalRewards = $subscriptions->where('status', 'active')->sum('current_rewards');
        $activeCount = $subscriptions->where('status', 'active')->count();

        return view('user.crypto-staking.subscriptions', compact(
            'subscriptions', 
            'settings', 
            'title',
            'totalStaked',
            'totalRewards',
            'activeCount'
        ));
    }

    /**
     * Cancel/withdraw from staking
     */
    public function cancel($id)
    {
        $subscription = StakingSubscription::with(['stakingPlan'])->findOrFail($id);
        $user = Auth::user();

        // Verify ownership
        if ($subscription->user_id !== $user->id) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if already completed/cancelled
        if ($subscription->status !== 'active') {
            return back()->with('error', 'This subscription is not active.');
        }

        DB::beginTransaction();
        try {
            $settings = Settings::first();

            // Calculate penalty if early withdrawal
            $penalty = $subscription->calculatePenalty();
            $netRewards = $subscription->current_rewards - $penalty;
            $totalReturn = $subscription->amount_staked + $subscription->compounded_amount + $netRewards;

            // Update user balance
            $user->account_bal += $totalReturn;
            $user->save();

            // Update subscription
            $subscription->status = 'cancelled';
            $subscription->save();

            // Decrement plan total staked
            $subscription->stakingPlan->decrement('current_total_staked', $subscription->amount_staked + $subscription->compounded_amount);

            // Create transaction record
            Tp_Transaction::create([
                'user' => $user->id,
                'plan' => "Staking Cancelled: {$subscription->stakingPlan->name}",
                'amount' => $totalReturn,
                'type' => 'Crypto Staking Return',
                'status' => 'Processed',
            ]);

            // Send email
            $message = "Your staking subscription for {$subscription->stakingPlan->name} has been cancelled. " .
                      ($penalty > 0 ? "Early withdrawal penalty: {$settings->currency}" . number_format($penalty, 2) . ". " : "") .
                      "Total returned to your account: {$settings->currency}" . number_format($totalReturn, 2);
            
            Mail::to($user->email)->send(new NewNotification(
                $message,
                'Staking Subscription Cancelled',
                $user->name
            ));

            DB::commit();

            return back()->with('success', 'Subscription cancelled successfully! Funds returned to your account.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Staking cancellation error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * View staking leaderboard
     */
    public function leaderboard(Request $request)
    {
        $settings = Settings::first();

        // Check if leaderboard is enabled
        if ($settings->show_staking_leaderboard !== 'on') {
            return redirect()->route('dashboard')->with('error', 'Leaderboard is currently disabled.');
        }

        $title = 'Staking Leaderboard';
        $filter = $request->input('crypto', 'all');

        // Build query
        $query = StakingSubscription::with(['user', 'stakingPlan.cryptoPrice'])
            ->select('user_id', DB::raw('SUM(amount_staked + compounded_amount) as total_staked'), 
                    DB::raw('SUM(current_rewards) as total_rewards'), 
                    DB::raw('COUNT(*) as stake_count'))
            ->groupBy('user_id');

        // Filter by crypto if specified
        if ($filter !== 'all') {
            $query->whereHas('stakingPlan', function($q) use ($filter) {
                $q->where('coin_symbol', $filter);
            });
        }

        $topStakers = $query->orderByDesc('total_rewards')->limit(50)->get();

        // Add avg_apr to each staker
        foreach ($topStakers as $staker) {
            $avgApr = StakingSubscription::where('user_id', $staker->user_id)
                ->where('status', 'active')
                ->avg('apr_at_subscription');
            $staker->avg_apr = $avgApr ?? 0;
        }

        // Calculate stats
        $totalRewards = StakingSubscription::sum('current_rewards');
        $totalStaked = StakingSubscription::sum(DB::raw('amount_staked + compounded_amount'));
        $activeStakes = StakingSubscription::where('status', 'active')->count();
        $totalStakers = StakingSubscription::distinct('user_id')->count('user_id');

        // Get current user's rank
        $user = Auth::user();
        $userRank = null;

        $allUsers = StakingSubscription::select('user_id', DB::raw('SUM(current_rewards) as total_rewards'))
            ->groupBy('user_id')
            ->orderByDesc('total_rewards')
            ->pluck('user_id')
            ->toArray();

        $userPosition = array_search($user->id, $allUsers);
        if ($userPosition !== false) {
            $userRank = $userPosition + 1;
        }

        // Get available cryptos for filter
        $cryptos = CryptoStakingPlan::with('cryptoPrice')
            ->where('status', 'active')
            ->get()
            ->pluck('cryptoPrice')
            ->unique('coin_symbol')
            ->filter();

        return view('user.crypto-staking.leaderboard', compact(
            'topStakers', 
            'settings', 
            'title', 
            'totalRewards',
            'totalStaked',
            'activeStakes',
            'totalStakers',
            'userRank',
            'cryptos'
        ));
    }

    /**
     * Calculate end date based on lock duration
     */
    private function calculateEndDate($startDate, $duration)
    {
        // Parse duration (e.g., "30 Days", "3 Months", "Flexible")
        if ($duration === 'Flexible') {
            return Carbon::parse($startDate)->addYears(10); // Far future for flexible
        }

        $parts = explode(' ', $duration);
        $value = (int) $parts[0];
        $unit = strtolower($parts[1] ?? 'days');

        $endDate = Carbon::parse($startDate);

        if (str_contains($unit, 'day')) {
            $endDate->addDays($value);
        } elseif (str_contains($unit, 'week')) {
            $endDate->addWeeks($value);
        } elseif (str_contains($unit, 'month')) {
            $endDate->addMonths($value);
        } elseif (str_contains($unit, 'year')) {
            $endDate->addYears($value);
        }

        return $endDate;
    }
}
