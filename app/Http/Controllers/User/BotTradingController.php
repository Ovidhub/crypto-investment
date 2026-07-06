<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingBot;
use App\Models\BotSubscription;
use App\Models\BotTrade;
use App\Models\Settings;

class BotTradingController extends Controller
{
    /**
     * Display all available trading bots
     */
    public function index()
    {
        $bots = TradingBot::where('status', 'active')
            ->withCount('activeSubscriptions')
            ->orderBy('roi_percentage', 'desc')
            ->get();

        return view('user.bot-trading.index', compact('bots'));
    }

    /**
     * Show single bot details
     */
    public function show(TradingBot $bot)
    {
        if ($bot->status !== 'active') {
            return redirect()->route('user.bot-trading.index')
                ->with('error', 'This bot is not available');
        }

        $bot->loadCount('activeSubscriptions');
        $recentTrades = $bot->trades()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('user.bot-trading.show', compact('bot', 'recentTrades'));
    }

    /**
     * Subscribe to a bot
     */
    public function subscribe(Request $request, TradingBot $bot)
    {
        $settings = Settings::where('id', '=', '1')->first();
        $user = auth()->user();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $bot->min_capital . '|max:' . $bot->max_capital,
            'duration' => 'required|in:1 Days,2 Days,3 Days,4 Days,5 Days,6 Days,7 Days,30 Days,60 Days,90 Days',
        ]);

        // Check if user has sufficient balance
        if ($user->account_bal < $validated['amount']) {
            return back()->with('error', 'Insufficient account balance');
        }

        // Check if user already has active subscription to this bot
        $existingSubscription = BotSubscription::where('user_id', $user->id)
            ->where('trading_bot_id', $bot->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return back()->with('error', 'You already have an active subscription to this bot');
        }

        // Calculate end date
        $durationDays = (int) str_replace(' Days', '', $validated['duration']);
        $endDate = now()->addDays($durationDays);

        // Create subscription
        $subscription = BotSubscription::create([
            'user_id' => $user->id,
            'trading_bot_id' => $bot->id,
            'amount_invested' => $validated['amount'],
            'current_profit' => 0,
            'start_date' => now(),
            'end_date' => $endDate,
            'duration' => $validated['duration'],
            'status' => 'active',
        ]);

        // Deduct amount from user balance
        $user->decrement('account_bal', $validated['amount']);

        // Send notification
        $user->notify(new \App\Notifications\InvestmentMail([
            'title' => 'Bot Trading Subscription',
            'content' => 'You have successfully subscribed to ' . $bot->name . ' with $' . number_format($validated['amount'], 2),
            'url' => route('user.bot-trading.subscriptions'),
        ]));

        // Send admin notification if enabled
        if ($settings->send_investment_email == 1) {
            \App\Models\Admin::first()->notify(new \App\Notifications\InvestmentMail([
                'title' => 'New Bot Trading Subscription',
                'content' => $user->name . ' subscribed to ' . $bot->name . ' with $' . number_format($validated['amount'], 2),
                'url' => route('admin.bot-trading.subscribers', $bot),
            ]));
        }

        return redirect()->route('user.bot-trading.subscriptions')
            ->with('success', 'Successfully subscribed to ' . $bot->name);
    }

    /**
     * Show user's subscriptions
     */
    public function subscriptions()
    {
        $subscriptions = BotSubscription::where('user_id', auth()->id())
            ->with('tradingBot')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.bot-trading.subscriptions', compact('subscriptions'));
    }

    /**
     * Show user's trades
     */
    public function trades()
    {
        $trades = BotTrade::where('user_id', auth()->id())
            ->with(['tradingBot', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalWins = BotTrade::where('user_id', auth()->id())->where('outcome', 'win')->count();
        $totalLosses = BotTrade::where('user_id', auth()->id())->where('outcome', 'loss')->count();
        $totalProfit = BotTrade::where('user_id', auth()->id())->sum('profit_loss');

        return view('user.bot-trading.trades', compact('trades', 'totalWins', 'totalLosses', 'totalProfit'));
    }

    /**
     * Cancel subscription
     */
    public function cancel(BotSubscription $subscription)
    {
        // Verify ownership
        if ($subscription->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action');
        }

        if ($subscription->status !== 'active') {
            return back()->with('error', 'This subscription is not active');
        }

        // Return invested amount + current profit to user
        $totalReturn = $subscription->amount_invested + $subscription->current_profit + ($subscription->manual_profit_adjustment ?? 0);
        
        $subscription->user->increment('account_bal', $totalReturn);

        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now(),
        ]);

        return redirect()->route('user.bot-trading.subscriptions')
            ->with('success', 'Subscription cancelled. $' . number_format($totalReturn, 2) . ' returned to your account');
    }
}
