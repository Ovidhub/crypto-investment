<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TradingBot;
use App\Models\BotSubscription;
use App\Models\BotTrade;
use App\Models\CryptoPrice;
use App\Models\Tp_Transaction;
use Illuminate\Support\Facades\Log;

class ProcessBotTrading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get all active bots with auto trading enabled
        $bots = TradingBot::where('status', 'active')
            ->where('auto_trade', 'on')
            ->get();

        foreach ($bots as $bot) {
            $this->processBotTrades($bot);
        }
    }

    /**
     * Process trades for a specific bot
     */
    protected function processBotTrades(TradingBot $bot)
    {
        // Get all active subscriptions for this bot
        $subscriptions = $bot->activeSubscriptions()
            ->where(function($query) use ($bot) {
                $query->whereNull('last_trade_at')
                    ->orWhereRaw('TIMESTAMPDIFF(MINUTE, last_trade_at, NOW()) >= ?', [$bot->getIntervalMinutes()]);
            })
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $this->executeTrade($subscription, $bot);
            } catch (\Exception $e) {
                Log::error('Bot trading error for subscription ' . $subscription->id . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Execute a single trade
     */
    protected function executeTrade(BotSubscription $subscription, TradingBot $bot)
    {
        // Check if subscription has expired
        if ($subscription->hasExpired()) {
            $subscription->update(['status' => 'completed']);
            
            // Return invested amount + profit to user
            $totalReturn = $subscription->amount_invested + $subscription->current_profit + ($subscription->manual_profit_adjustment ?? 0);
            $totalProfit = $subscription->current_profit + ($subscription->manual_profit_adjustment ?? 0);
            
            $subscription->user->increment('account_bal', $totalReturn);
            // Add profit to ROI column for dashboard display
            if ($totalProfit > 0) {
                $subscription->user->increment('roi', $totalProfit);
            }
            
            return;
        }

        // Get random crypto asset
        $crypto = CryptoPrice::inRandomOrder()->first();
        
        if (!$crypto) {
            Log::warning('No crypto assets found for bot trading');
            return;
        }

        // Determine win or loss based on bot's win rate
        $isWin = (rand(1, 100) <= $bot->win_rate);
        
        // Calculate profit/loss percentage
        if ($isWin) {
            $profitPercentage = $this->getRandomDecimal($bot->min_profit, $bot->max_profit);
        } else {
            $profitPercentage = $this->getRandomDecimal($bot->min_loss, $bot->max_loss) * -1;
        }

        // Calculate actual profit/loss amount
        $profitLoss = ($subscription->amount_invested * $profitPercentage) / 100;

        // Get entry and exit prices (simulated based on current price)
        $entryPrice = $crypto->price_usd ?? 1000;
        $exitPrice = $entryPrice * (1 + ($profitPercentage / 100));

        // Create trade record
        $trade = BotTrade::create([
            'bot_subscription_id' => $subscription->id,
            'trading_bot_id' => $bot->id,
            'user_id' => $subscription->user_id,
            'asset_symbol' => $crypto->coin_symbol ?? 'BTC',
            'asset_name' => $crypto->name ?? 'Bitcoin',
            'asset_logo' => $crypto->logo ?? null,
            'entry_price' => $entryPrice,
            'exit_price' => $exitPrice,
            'amount' => $subscription->amount_invested / $entryPrice, // Calculate units
            'profit_loss' => $profitLoss,
            'profit_percentage' => $profitPercentage,
            'outcome' => $isWin ? 'win' : 'loss',
            'trade_type' => 'automated',
            'opened_at' => now()->subMinutes(rand(5, 60)),
            'closed_at' => now(),
        ]);

        // Update subscription
        $subscription->increment('current_profit', $profitLoss);
        $subscription->update(['last_trade_at' => now()]);

        // Update user balance
        $subscription->user->increment('account_bal', $profitLoss);
        
        // Add profit to ROI column for dashboard display (only positive profits)
        if ($profitLoss > 0) {
            $subscription->user->increment('roi', $profitLoss);
        }

        // Update bot statistics
        $bot->increment('total_trades');
        $bot->increment('total_profit', $profitLoss);

        // Create transaction record
        Tp_Transaction::create([
            'user' => $subscription->user_id,
            'plan' => 'Bot Trading: ' . $bot->name . ' - ' . $crypto->coin_symbol,
            'amount' => abs($profitLoss),
            'type' => $isWin ? 'Profit' : 'Debit',
        ]);

        // Send notification to user
        $subscription->user->notify(new \App\Notifications\InvestmentMail([
            'title' => 'Bot Trading Update',
            'content' => sprintf(
                '%s executed a %s trade on %s. %s: $%s',
                $bot->name,
                $isWin ? 'winning' : 'losing',
                $crypto->name ?? 'Unknown',
                $isWin ? 'Profit' : 'Loss',
                number_format(abs($profitLoss), 2)
            ),
            'url' => route('user.bot-trading.trades'),
        ]));
    }

    /**
     * Get random decimal between min and max
     */
    protected function getRandomDecimal($min, $max)
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}
