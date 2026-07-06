<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\NewNotification;
use App\Models\ExpertTrader;
use App\Models\ExpertTraderSubscriber;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CopyTradingController extends Controller
{
    // Browse available expert traders
    public function index()
    {
        $expertTraders = ExpertTrader::where('status', 'active')->orderByDesc('roi_percentage')->get();
        $settings = Settings::first();

        // Get user's active subscriptions with subscription IDs
        $userSubscriptions = ExpertTraderSubscriber::where('user_id', Auth::id())
            ->where('status', 'active')
            ->pluck('expert_trader_id')
            ->toArray();

        // Get subscription IDs mapped by trader ID
        $subscriptionIds = ExpertTraderSubscriber::where('user_id', Auth::id())
            ->where('status', 'active')
            ->pluck('id', 'expert_trader_id')
            ->toArray();

        $title = 'Copy Trading';
        return view('user.copytrading.index', compact('expertTraders', 'settings', 'title', 'userSubscriptions', 'subscriptionIds'));
    }

    // Subscribe to an expert trader
    public function subscribe(Request $request)
    {
        $request->validate([
            'expert_trader_id' => 'required|exists:expert_traders,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $expertTrader = ExpertTrader::findOrFail($request->expert_trader_id);
        $settings = Settings::first();

        // Check if trader is active
        if ($expertTrader->status !== 'active') {
            return back()->with('error', 'This expert trader is currently not accepting new copiers.');
        }

        // Check if trader is at capacity
        if ($expertTrader->isAtCapacity()) {
            return back()->with('error', 'This expert trader has reached maximum capacity. Please try another trader.');
        }

        // Validate investment amount
        if ($request->amount < $expertTrader->min_capital) {
            return back()->with('error', "Minimum copy amount is {$settings->currency}" . number_format($expertTrader->min_capital, 2));
        }

        if ($request->amount > $expertTrader->max_capital) {
            return back()->with('error', "Maximum copy amount is {$settings->currency}" . number_format($expertTrader->max_capital, 2));
        }

        // Check user balance
        if ($user->account_bal < $request->amount) {
            return back()->with('error', 'Insufficient balance. Please fund your account.');
        }

        // Check if user already has active subscription with this trader
        $existingSubscription = ExpertTraderSubscriber::where('user_id', $user->id)
            ->where('expert_trader_id', $expertTrader->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return back()->with('error', 'You already have an active subscription with this expert trader.');
        }

        try {
            DB::beginTransaction();

            // Calculate duration
            $duration = $expertTrader->duration;
            $startDate = Carbon::now();
            $endDate = $this->calculateEndDate($startDate, $duration);

            // Create subscription
            $subscription = ExpertTraderSubscriber::create([
                'expert_trader_id' => $expertTrader->id,
                'user_id' => $user->id,
                'amount_invested' => $request->amount,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'current_profit' => 0,
                'status' => 'active',
            ]);

            // Deduct from user balance
            $user->account_bal -= $request->amount;
            $user->save();

            // Increment trader followers
            $expertTrader->increment('total_followers');

            // Create transaction record
            Tp_Transaction::create([
                'user' => $user->id,
                'plan' => "Copy Trading: {$expertTrader->name}",
                'amount' => $request->amount,
                'type' => 'Copy Trading Subscription',
                'status' => 'Processed',
            ]);

            // Send email notification
            $message = "You have successfully subscribed to copy {$expertTrader->name} with {$settings->currency}" . number_format($request->amount, 2) . ". Duration: {$duration}";
            $subject = "Copy Trading Subscription Successful";
            Mail::to($user->email)->send(new NewNotification($message, $subject, $user->email));

            DB::commit();

            return redirect()->route('copytrading.subscriptions')->with('success', 'Successfully subscribed to expert trader!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Copy trading subscription error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // View user's copy trading subscriptions
    public function mySubscriptions()
    {
        $user = Auth::user();
        $subscriptions = ExpertTraderSubscriber::with('expertTrader')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $settings = Settings::first();
        $title = 'My Copy Trading Subscriptions';

        return view('user.copytrading.subscriptions', compact('subscriptions', 'settings', 'title'));
    }

    // Cancel subscription
    public function cancelSubscription($id)
    {
        $user = Auth::user();
        $subscription = ExpertTraderSubscriber::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($subscription->status !== 'active') {
            return back()->with('error', 'This subscription is not active.');
        }

        try {
            DB::beginTransaction();

            // Return invested amount + profit to user
            $totalReturn = $subscription->amount_invested + $subscription->current_profit;
            $user->account_bal += $totalReturn;
            $user->save();

            // Update subscription status
            $subscription->status = 'cancelled';
            $subscription->save();

            // Decrement trader followers
            $subscription->expertTrader->decrement('total_followers');

            // Create transaction record
            Tp_Transaction::create([
                'user' => $user->id,
                'plan' => "Copy Trading Cancelled: {$subscription->expertTrader->name}",
                'amount' => $totalReturn,
                'type' => 'Copy Trading Return',
                'status' => 'Processed',
            ]);

            DB::commit();

            $settings = Settings::first();
            $message = "Your copy trading subscription with {$subscription->expertTrader->name} has been cancelled. Total return: {$settings->currency}" . number_format($totalReturn, 2);
            $subject = "Copy Trading Subscription Cancelled";
            Mail::to($user->email)->send(new NewNotification($message, $subject, $user->email));

            return back()->with('success', 'Subscription cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }

    // Helper function to calculate end date
    private function calculateEndDate($startDate, $duration)
    {
        // Parse duration (e.g., "30 Days", "3 Months")
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
