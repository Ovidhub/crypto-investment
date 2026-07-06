<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewNotification;
use App\Models\ExpertTrader;
use App\Models\ExpertTraderSubscriber;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CopyTradingController extends Controller
{
    // List all expert traders
    public function index()
    {
        $expertTraders = ExpertTrader::withCount('activeSubscribers')->orderByDesc('id')->get();
        $settings = Settings::first();
        $title = 'Expert Traders';

        return view('admin.copytrading.index', compact('expertTraders', 'settings', 'title'));
    }

    // Show create form
    public function create()
    {
        $settings = Settings::first();
        $title = 'Create Expert Trader';

        return view('admin.copytrading.create', compact('settings', 'title'));
    }

    // Store new expert trader
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string',
            'min_capital' => 'required|numeric|min:0',
            'max_capital' => 'required|numeric|min:0',
            'max_followers' => 'required|integer|min:1',
            'preset_followers' => 'nullable|integer|min:0',
            'duration' => 'required|string',
            'roi_percentage' => 'required|numeric|min:0|max:100',
            'risk_level' => 'required|in:Low,Medium,High',
            'active_trades' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'auto_distribute' => 'nullable|in:on,off',
            'distribution_interval' => 'nullable|in:5 Minutes,30 Minutes,Hourly,Daily,Weekly,Monthly',
            'distribution_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = $request->except('photo');
        
        // Handle checkbox value
        $data['auto_distribute'] = $request->has('auto_distribute') ? 'on' : 'off';

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/expert_traders', $filename);
            $data['photo'] = $filename;
        }

        ExpertTrader::create($data);

        return redirect()->route('admin.copytrading.index')->with('success', 'Expert Trader created successfully!');
    }

    // Show edit form
    public function edit($id)
    {
        $trader = ExpertTrader::findOrFail($id);
        $settings = Settings::first();
        $title = 'Edit Expert Trader';

        return view('admin.copytrading.edit', compact('trader', 'settings', 'title'));
    }

    // Update expert trader
    public function update(Request $request, $id)
    {
        $trader = ExpertTrader::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string',
            'min_capital' => 'required|numeric|min:0',
            'max_capital' => 'required|numeric|min:0',
            'max_followers' => 'required|integer|min:1',
            'preset_followers' => 'nullable|integer|min:0',
            'duration' => 'required|string',
            'roi_percentage' => 'required|numeric|min:0|max:100',
            'risk_level' => 'required|in:Low,Medium,High',
            'active_trades' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'auto_distribute' => 'nullable|in:on,off',
            'distribution_interval' => 'nullable|in:5 Minutes,30 Minutes,Hourly,Daily,Weekly,Monthly',
            'distribution_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $data = $request->except('photo');
        
        // Handle checkbox value
        $data['auto_distribute'] = $request->has('auto_distribute') ? 'on' : 'off';

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($trader->photo) {
                Storage::delete('public/expert_traders/' . $trader->photo);
            }
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('public/expert_traders', $filename);
            $data['photo'] = $filename;
        }

        $trader->update($data);

        return redirect()->route('admin.copytrading.index')->with('success', 'Expert Trader updated successfully!');
    }

    // Toggle status
    public function toggleStatus($id)
    {
        $trader = ExpertTrader::findOrFail($id);
        $trader->status = $trader->status === 'active' ? 'inactive' : 'active';
        $trader->save();

        return back()->with('success', 'Status updated successfully!');
    }

    // Delete expert trader
    public function destroy($id)
    {
        $trader = ExpertTrader::findOrFail($id);

        // Check if has active subscribers
        if ($trader->activeSubscribers()->count() > 0) {
            return back()->with('error', 'Cannot delete trader with active subscribers. Please cancel all subscriptions first.');
        }

        // Delete photo
        if ($trader->photo) {
            Storage::delete('public/expert_traders/' . $trader->photo);
        }

        $trader->delete();

        return back()->with('success', 'Expert Trader deleted successfully!');
    }

    // View subscribers of a trader
    public function subscribers($id)
    {
        $trader = ExpertTrader::with('subscribers.user')->findOrFail($id);
        $subscribers = $trader->subscribers;
        $settings = Settings::first();
        $title = 'Subscribers - ' . $trader->name;

        return view('admin.copytrading.subscribers', compact('trader', 'subscribers', 'settings', 'title'));
    }

    // Show distribute profit form
    public function showDistribute($id)
    {
        $trader = ExpertTrader::with('activeSubscribers.user')->findOrFail($id);
        $activeSubscribers = $trader->activeSubscribers;
        $settings = Settings::first();
        $title = 'Distribute Profit - ' . $trader->name;

        return view('admin.copytrading.distribute', compact('trader', 'activeSubscribers', 'settings', 'title'));
    }

    // Distribute profit to all active subscribers
    public function distributeProfit(Request $request, $id)
    {
        $request->validate([
            'profit_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $trader = ExpertTrader::with('activeSubscribers.user')->findOrFail($id);
        $profitPercentage = $request->profit_percentage;
        $settings = Settings::first();

        if ($trader->activeSubscribers->count() === 0) {
            return back()->with('error', 'No active subscribers to distribute profit to.');
        }

        try {
            DB::beginTransaction();

            $totalProfitDistributed = 0;
            $subscribersUpdated = 0;

            foreach ($trader->activeSubscribers as $subscription) {
                // Calculate profit
                $profit = ($subscription->amount_invested * $profitPercentage) / 100;

                // Update subscription
                $subscription->current_profit += $profit;
                $subscription->last_profit_added = now();
                $subscription->save();

                // Update user balance
                $user = $subscription->user;
                $user->account_bal += $profit;
                $user->save();

                // Create transaction record
                Tp_Transaction::create([
                    'user' => $user->id,
                    'plan' => "Copy Trading Profit: {$trader->name}",
                    'amount' => $profit,
                    'type' => 'Copy Trading Profit',
                    'status' => 'Processed',
                ]);

                // Send email notification
                $message = "You have received {$settings->currency}" . number_format($profit, 2) . " profit ({$profitPercentage}%) from copy trading {$trader->name}.";
                $subject = "Copy Trading Profit Received";
                Mail::to($user->email)->send(new NewNotification($message, $subject, $user->email));

                $totalProfitDistributed += $profit;
                $subscribersUpdated++;
            }

            // Update trader stats
            $trader->total_profit += $totalProfitDistributed;
            $trader->save();

            DB::commit();

            return back()->with('success', "Profit distributed successfully to {$subscribersUpdated} subscribers. Total: {$settings->currency}" . number_format($totalProfitDistributed, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while distributing profit. Please try again.');
        }
    }
}
