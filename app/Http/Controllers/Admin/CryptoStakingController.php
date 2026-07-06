<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CryptoStakingPlan;
use App\Models\StakingSubscription;
use App\Models\CryptoPrice;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use App\Models\User;
use App\Mail\NewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CryptoStakingController extends Controller
{
    /**
     * Display all staking plans
     */
    public function index()
    {
        $plans = CryptoStakingPlan::with('cryptoPrice')->orderBy('created_at', 'desc')->get();
        $settings = Settings::first();
        
        return view('admin.crypto-staking.index', compact('plans', 'settings'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $cryptos = CryptoPrice::orderBy('name')->get();
        $settings = Settings::first();
        
        return view('admin.crypto-staking.create', compact('cryptos', 'settings'));
    }

    /**
     * Store new staking plan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'coin_symbol' => 'required|exists:crypto_prices,coin_symbol',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'apr_percentage' => 'required|numeric|min:0.01|max:100',
            'min_stake' => 'required|numeric|min:1',
            'max_stake' => 'required|numeric|gt:min_stake',
            'max_total_staked' => 'nullable|numeric|min:0',
            'lock_duration' => 'required|string',
            'reward_interval' => 'required|string',
            'early_withdrawal_penalty' => 'required|numeric|min:0|max:50',
            'compound_enabled' => 'required|in:on,off',
            'auto_distribute' => 'required|in:on,off',
            'distribution_paused' => 'nullable|in:on,off',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/staking-plans', $photoName);
            $validated['photo'] = 'staking-plans/' . $photoName;
        }

        $validated['distribution_paused'] = $request->input('distribution_paused', 'off');

        CryptoStakingPlan::create($validated);

        return redirect()->route('admin.crypto-staking.index')
            ->with('success', 'Crypto staking plan created successfully!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $plan = CryptoStakingPlan::with('activeSubscriptions')->findOrFail($id);
        $cryptos = CryptoPrice::orderBy('name')->get();
        $settings = Settings::first();
        
        return view('admin.crypto-staking.edit', compact('plan', 'cryptos', 'settings'));
    }

    /**
     * Update staking plan
     */
    public function update(Request $request, $id)
    {
        $plan = CryptoStakingPlan::findOrFail($id);

        $validated = $request->validate([
            'coin_symbol' => 'required|exists:crypto_prices,coin_symbol',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'apr_percentage' => 'required|numeric|min:0.01|max:100',
            'min_stake' => 'required|numeric|min:1',
            'max_stake' => 'required|numeric|gt:min_stake',
            'max_total_staked' => 'nullable|numeric|min:0',
            'lock_duration' => 'required|string',
            'reward_interval' => 'required|string',
            'early_withdrawal_penalty' => 'required|numeric|min:0|max:50',
            'compound_enabled' => 'required|in:on,off',
            'auto_distribute' => 'required|in:on,off',
            'distribution_paused' => 'nullable|in:on,off',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($plan->photo) {
                Storage::disk('public')->delete($plan->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/staking-plans', $photoName);
            $validated['photo'] = 'staking-plans/' . $photoName;
        }

        $validated['distribution_paused'] = $request->input('distribution_paused', 'off');

        $plan->update($validated);

        return redirect()->route('admin.crypto-staking.index')
            ->with('success', 'Staking plan updated successfully! Note: APR changes only affect new subscriptions.');
    }

    /**
     * Delete staking plan
     */
    public function destroy($id)
    {
        $plan = CryptoStakingPlan::findOrFail($id);

        // Check for active subscriptions
        $activeCount = $plan->activeSubscriptions()->count();
        if ($activeCount > 0) {
            return back()->with('error', "Cannot delete plan with {$activeCount} active subscriptions. Force complete them first.");
        }

        // Delete photo
        if ($plan->photo) {
            Storage::disk('public')->delete($plan->photo);
        }

        $plan->delete();

        return redirect()->route('admin.crypto-staking.index')
            ->with('success', 'Staking plan deleted successfully!');
    }

    /**
     * Toggle pause distribution
     */
    public function togglePause($id)
    {
        $plan = CryptoStakingPlan::findOrFail($id);
        
        $plan->distribution_paused = $plan->distribution_paused === 'on' ? 'off' : 'on';
        $plan->save();

        $status = $plan->distribution_paused === 'on' ? 'paused' : 'resumed';

        return back()->with('success', "Distribution {$status} for {$plan->name}!");
    }

    /**
     * View subscribers for a plan
     */
    public function subscribers($id)
    {
        $plan = CryptoStakingPlan::with(['cryptoPrice', 'subscriptions.user'])->findOrFail($id);
        $settings = Settings::first();
        
        return view('admin.crypto-staking.subscribers', compact('plan', 'settings'));
    }

    /**
     * Adjust reward manually
     */
    public function adjustReward(Request $request, $id)
    {
        $subscription = StakingSubscription::with(['stakingPlan', 'user'])->findOrFail($id);

        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $amount = $validated['amount'];
            $isAdd = $validated['adjustment_type'] === 'add';

            // Update subscription rewards
            if ($isAdd) {
                $subscription->current_rewards += $amount;
                $subscription->user->account_bal += $amount;
                $subscription->user->roi += $amount;
                $txType = 'Staking Reward Adjustment (Credit)';
            } else {
                $subscription->current_rewards = max(0, $subscription->current_rewards - $amount);
                $subscription->user->account_bal -= $amount;
                $txType = 'Staking Reward Adjustment (Debit)';
            }

            $subscription->save();
            $subscription->user->save();

            // Create transaction
            Tp_Transaction::create([
                'user' => $subscription->user_id,
                'plan' => "Manual Adjustment: {$subscription->stakingPlan->name}",
                'amount' => $amount,
                'type' => $txType,
                'status' => 'Processed',
            ]);

            DB::commit();

            return back()->with('success', 'Reward adjusted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to adjust reward: ' . $e->getMessage());
        }
    }

    /**
     * Force complete single subscription
     */
    public function forceCompleteSingle(Request $request, $id)
    {
        $subscription = StakingSubscription::with(['stakingPlan', 'user'])->findOrFail($id);

        if ($subscription->status !== 'active') {
            return back()->with('error', 'Subscription is not active.');
        }

        $validated = $request->validate([
            'apply_penalty' => 'required|boolean',
            'reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $penalty = $validated['apply_penalty'] ? $subscription->calculatePenalty() : 0;
            $totalReturn = $subscription->amount_staked + $subscription->compounded_amount + ($subscription->current_rewards - $penalty);

            // Update user balance
            $subscription->user->account_bal += $totalReturn;
            $subscription->user->save();

            // Update subscription
            $subscription->status = 'force_completed';
            $subscription->force_completed_by = Auth::guard('admin')->id();
            $subscription->force_completed_at = now();
            $subscription->force_complete_reason = $validated['reason'];
            $subscription->save();

            // Decrement plan total
            $subscription->stakingPlan->decrement('current_total_staked', $subscription->amount_staked + $subscription->compounded_amount);

            // Create transaction
            Tp_Transaction::create([
                'user' => $subscription->user_id,
                'plan' => "Force Completed: {$subscription->stakingPlan->name}",
                'amount' => $totalReturn,
                'type' => 'Staking Force Return',
                'status' => 'Processed',
            ]);

            // Send notification
            $settings = Settings::first();
            $message = "Your staking subscription for {$subscription->stakingPlan->name} has been force completed by admin. " .
                      ($penalty > 0 ? "Penalty applied: {$settings->currency}" . number_format($penalty, 2) . ". " : "") .
                      "Total returned: {$settings->currency}" . number_format($totalReturn, 2);
            
            Mail::to($subscription->user->email)->send(new NewNotification(
                $message,
                'Staking Subscription Force Completed',
                $subscription->user->name
            ));

            DB::commit();

            return back()->with('success', 'Subscription force completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to force complete: ' . $e->getMessage());
        }
    }

    /**
     * Force complete all subscriptions for a plan
     */
    public function forceCompleteAll(Request $request, $id)
    {
        $plan = CryptoStakingPlan::with('activeSubscriptions.user')->findOrFail($id);

        $validated = $request->validate([
            'apply_penalty' => 'required|boolean',
            'reason' => 'required|string|max:500',
        ]);

        $activeSubscriptions = $plan->activeSubscriptions;

        if ($activeSubscriptions->isEmpty()) {
            return back()->with('error', 'No active subscriptions to complete.');
        }

        DB::beginTransaction();
        try {
            $settings = Settings::first();
            $adminId = Auth::guard('admin')->id();
            $completedCount = 0;

            foreach ($activeSubscriptions as $subscription) {
                $penalty = $validated['apply_penalty'] ? $subscription->calculatePenalty() : 0;
                $totalReturn = $subscription->amount_staked + $subscription->compounded_amount + ($subscription->current_rewards - $penalty);

                // Update user balance
                $subscription->user->account_bal += $totalReturn;
                $subscription->user->save();

                // Update subscription
                $subscription->status = 'force_completed';
                $subscription->force_completed_by = $adminId;
                $subscription->force_completed_at = now();
                $subscription->force_complete_reason = $validated['reason'];
                $subscription->save();

                // Create transaction
                Tp_Transaction::create([
                    'user' => $subscription->user_id,
                    'plan' => "Force Completed: {$plan->name}",
                    'amount' => $totalReturn,
                    'type' => 'Staking Force Return',
                    'status' => 'Processed',
                ]);

                // Send notification
                $message = "Your staking subscription for {$plan->name} has been force completed by admin. " .
                          ($penalty > 0 ? "Penalty applied: {$settings->currency}" . number_format($penalty, 2) . ". " : "") .
                          "Total returned: {$settings->currency}" . number_format($totalReturn, 2);
                
                try {
                    Mail::to($subscription->user->email)->send(new NewNotification(
                        $message,
                        'Staking Subscription Force Completed',
                        $subscription->user->name
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send force completion email: ' . $e->getMessage());
                }

                $completedCount++;
            }

            // Reset plan total
            $plan->current_total_staked = 0;
            $plan->save();

            DB::commit();

            return redirect()->route('admin.crypto-staking.index')
                ->with('success', "Successfully force completed {$completedCount} subscriptions for {$plan->name}!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to force complete all: ' . $e->getMessage());
        }
    }

    /**
     * View leaderboard
     */
    public function leaderboard($planId = null)
    {
        $settings = Settings::first();
        
        if ($planId) {
            $plan = CryptoStakingPlan::findOrFail($planId);
            $topStakers = $plan->topStakers(50);
        } else {
            // Global leaderboard
            $topStakers = StakingSubscription::with(['user', 'stakingPlan.cryptoPrice'])
                ->select('user_id', DB::raw('SUM(amount_staked + compounded_amount) as total_staked'), DB::raw('SUM(current_rewards) as total_rewards'), DB::raw('COUNT(*) as stake_count'))
                ->groupBy('user_id')
                ->orderByDesc('total_rewards')
                ->limit(50)
                ->get();
        }

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

        $plans = CryptoStakingPlan::with('cryptoPrice')->where('status', 'active')->get();
        
        return view('admin.crypto-staking.leaderboard', compact('topStakers', 'plans', 'settings', 'planId', 'totalRewards', 'totalStaked', 'activeStakes', 'totalStakers'));
    }
}
