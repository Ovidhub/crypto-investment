<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradingBot;
use App\Models\BotSubscription;
use App\Models\BotTrade;
use App\Models\User;
use App\Models\CryptoPrice;
use App\Models\Tp_Transaction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BotTradingController extends Controller
{
    /**
     * Display a listing of trading bots
     */
    public function index()
    {
        $bots = TradingBot::withCount(['subscriptions', 'activeSubscriptions'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.bot-trading.index', compact('bots'));
    }

    /**
     * Show the form for creating a new bot
     */
    public function create()
    {
        return view('admin.bot-trading.create');
    }

    /**
     * Store a newly created bot
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'strategy_type' => 'required|in:Scalping,Day Trading,Swing Trading',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'win_rate' => 'required|numeric|min:0|max:100',
            'roi_percentage' => 'required|numeric|min:0',
            'min_capital' => 'required|numeric|min:0',
            'max_capital' => 'required|numeric|min:0',
            'auto_trade' => 'required|in:on,off',
            'trade_interval' => 'required|in:5 Minutes,30 Minutes,Hourly,Daily',
            'min_profit' => 'required|numeric|min:0',
            'max_profit' => 'required|numeric|min:0',
            'min_loss' => 'required|numeric|min:0',
            'max_loss' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('bot-photos', 'public');
            $validated['photo'] = $path;
        }

        TradingBot::create($validated);

        return redirect()->route('admin.bot-trading.index')
            ->with('success', 'Trading bot created successfully');
    }

    /**
     * Show the form for editing the specified bot
     */
    public function edit(TradingBot $bot)
    {
        return view('admin.bot-trading.edit', compact('bot'));
    }

    /**
     * Update the specified bot
     */
    public function update(Request $request, TradingBot $bot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'strategy_type' => 'required|in:Scalping,Day Trading,Swing Trading',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'win_rate' => 'required|numeric|min:0|max:100',
            'roi_percentage' => 'required|numeric|min:0',
            'total_trades' => 'nullable|integer|min:0',
            'total_profit' => 'nullable|numeric',
            'min_capital' => 'required|numeric|min:0',
            'max_capital' => 'required|numeric|min:0',
            'auto_trade' => 'required|in:on,off',
            'trade_interval' => 'required|in:5 Minutes,30 Minutes,Hourly,Daily',
            'min_profit' => 'required|numeric|min:0',
            'max_profit' => 'required|numeric|min:0',
            'min_loss' => 'required|numeric|min:0',
            'max_loss' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($bot->photo) {
                Storage::disk('public')->delete($bot->photo);
            }
            $path = $request->file('photo')->store('bot-photos', 'public');
            $validated['photo'] = $path;
        }

        $bot->update($validated);

        return redirect()->route('admin.bot-trading.index')
            ->with('success', 'Trading bot updated successfully');
    }

    /**
     * Remove the specified bot
     */
    public function destroy(TradingBot $bot)
    {
        // Check if bot has active subscriptions
        if ($bot->activeSubscriptions()->count() > 0) {
            return back()->with('error', 'Cannot delete bot with active subscriptions');
        }

        // Delete photo
        if ($bot->photo) {
            Storage::disk('public')->delete($bot->photo);
        }

        $bot->delete();

        return redirect()->route('admin.bot-trading.index')
            ->with('success', 'Trading bot deleted successfully');
    }

    /**
     * Show subscribers for a bot
     */
    public function subscribers(TradingBot $bot)
    {
        $subscriptions = $bot->subscriptions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bot-trading.subscribers', compact('bot', 'subscriptions'));
    }

    /**
     * Show trades for a subscription
     */
    public function trades(BotSubscription $subscription)
    {
        $trades = $subscription->trades()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bot-trading.trades', compact('subscription', 'trades'));
    }

    /**
     * Adjust subscription profit manually
     */
    public function adjustProfit(Request $request, BotSubscription $subscription)
    {
        $validated = $request->validate([
            'adjustment' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $subscription->update([
            'manual_profit_adjustment' => $validated['adjustment'],
            'last_manual_update_at' => now(),
            'last_manual_update_by' => auth('admin')->id(),
        ]);

        // Create transaction record
        Tp_Transaction::create([
            'user' => $subscription->user_id,
            'plan' => 'Bot Trading Adjustment: ' . $subscription->tradingBot->name,
            'amount' => $validated['adjustment'],
            'type' => $validated['adjustment'] >= 0 ? 'Profit' : 'Debit',
        ]);

        return back()->with('success', 'Profit adjusted successfully');
    }

    /**
     * Show form to create manual trade
     */
    public function createManualTrade(BotSubscription $subscription)
    {
        $cryptos = CryptoPrice::orderBy('name')->get();
        return view('admin.bot-trading.create-trade', compact('subscription', 'cryptos'));
    }

    /**
     * Store manual trade
     */
    public function storeManualTrade(Request $request, BotSubscription $subscription)
    {
        $validated = $request->validate([
            'asset_symbol' => 'required|string',
            'asset_name' => 'required|string',
            'asset_logo' => 'nullable|string',
            'entry_price' => 'required|numeric|min:0',
            'exit_price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'outcome' => 'required|in:win,loss',
            'notes' => 'nullable|string',
            'opened_at' => 'nullable|date',
            'closed_at' => 'nullable|date',
        ]);

        // Calculate profit/loss
        $profitLoss = ($validated['exit_price'] - $validated['entry_price']) * $validated['amount'];
        if ($validated['outcome'] === 'loss') {
            $profitLoss = abs($profitLoss) * -1;
        }

        $profitPercentage = ($validated['entry_price'] > 0) 
            ? (($validated['exit_price'] - $validated['entry_price']) / $validated['entry_price']) * 100
            : 0;

        // Create trade
        $trade = BotTrade::create([
            'bot_subscription_id' => $subscription->id,
            'trading_bot_id' => $subscription->trading_bot_id,
            'user_id' => $subscription->user_id,
            'asset_symbol' => $validated['asset_symbol'],
            'asset_name' => $validated['asset_name'],
            'asset_logo' => $validated['asset_logo'],
            'entry_price' => $validated['entry_price'],
            'exit_price' => $validated['exit_price'],
            'amount' => $validated['amount'],
            'profit_loss' => $profitLoss,
            'profit_percentage' => $profitPercentage,
            'outcome' => $validated['outcome'],
            'trade_type' => 'manual',
            'created_by_admin_id' => auth('admin')->id(),
            'notes' => $validated['notes'],
            'opened_at' => $validated['opened_at'] ?? now()->subHour(),
            'closed_at' => $validated['closed_at'] ?? now(),
        ]);

        // Update subscription profit
        $subscription->increment('current_profit', $profitLoss);
        $subscription->update(['last_trade_at' => now()]);

        // Update user balance
        $user = $subscription->user;
        $user->increment('account_bal', $profitLoss);

        // Update bot stats
        $bot = $subscription->tradingBot;
        $bot->increment('total_trades');
        $bot->increment('total_profit', $profitLoss);

        // Create transaction
        Tp_Transaction::create([
            'user' => $user->id,
            'plan' => 'Bot Trading: ' . $bot->name,
            'amount' => abs($profitLoss),
            'type' => $validated['outcome'] === 'win' ? 'Profit' : 'Debit',
        ]);

        return redirect()->route('admin.bot-trading.trades', $subscription)
            ->with('success', 'Manual trade created successfully');
    }

    /**
     * Edit trade
     */
    public function editTrade(BotTrade $trade)
    {
        $cryptos = CryptoPrice::orderBy('name')->get();
        return view('admin.bot-trading.edit-trade', compact('trade', 'cryptos'));
    }

    /**
     * Update trade
     */
    public function updateTrade(Request $request, BotTrade $trade)
    {
        $validated = $request->validate([
            'asset_symbol' => 'required|string',
            'asset_name' => 'required|string',
            'asset_logo' => 'nullable|string',
            'entry_price' => 'required|numeric|min:0',
            'exit_price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'outcome' => 'required|in:win,loss',
            'notes' => 'nullable|string',
        ]);

        // Calculate difference in profit
        $oldProfit = $trade->profit_loss;

        $newProfitLoss = ($validated['exit_price'] - $validated['entry_price']) * $validated['amount'];
        if ($validated['outcome'] === 'loss') {
            $newProfitLoss = abs($newProfitLoss) * -1;
        }

        $profitPercentage = ($validated['entry_price'] > 0) 
            ? (($validated['exit_price'] - $validated['entry_price']) / $validated['entry_price']) * 100
            : 0;

        $profitDifference = $newProfitLoss - $oldProfit;

        // Update trade
        $trade->update([
            'asset_symbol' => $validated['asset_symbol'],
            'asset_name' => $validated['asset_name'],
            'asset_logo' => $validated['asset_logo'],
            'entry_price' => $validated['entry_price'],
            'exit_price' => $validated['exit_price'],
            'amount' => $validated['amount'],
            'profit_loss' => $newProfitLoss,
            'profit_percentage' => $profitPercentage,
            'outcome' => $validated['outcome'],
            'notes' => $validated['notes'],
        ]);

        // Update subscription profit
        $trade->subscription->increment('current_profit', $profitDifference);

        // Update user balance
        $trade->user->increment('account_bal', $profitDifference);

        // Update bot total profit
        $trade->tradingBot->increment('total_profit', $profitDifference);

        return redirect()->route('admin.bot-trading.trades', $trade->subscription)
            ->with('success', 'Trade updated successfully');
    }

    /**
     * Delete trade
     */
    public function deleteTrade(BotTrade $trade)
    {
        // Reverse the profit impact
        $trade->subscription->decrement('current_profit', $trade->profit_loss);
        $trade->user->decrement('account_bal', $trade->profit_loss);
        $trade->tradingBot->decrement('total_trades');
        $trade->tradingBot->decrement('total_profit', $trade->profit_loss);

        $subscriptionId = $trade->bot_subscription_id;
        $trade->delete();

        return redirect()->route('admin.bot-trading.trades', $subscriptionId)
            ->with('success', 'Trade deleted successfully');
    }
}
