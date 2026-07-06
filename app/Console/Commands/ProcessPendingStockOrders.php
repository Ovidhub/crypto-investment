<?php

namespace App\Console\Commands;

use App\Models\StockTrade;
use App\Models\StockPrice;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessPendingStockOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock-orders:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending stock limit and stop orders';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $settings = Settings::first();
        $processedCount = 0;
        $cancelledCount = 0;

        // Get all active pending orders (not expired)
        $pendingOrders = StockTrade::where('status', 'pending')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->get();

        $this->info('Found ' . $pendingOrders->count() . ' pending orders to check');

        foreach ($pendingOrders as $order) {
            // Get current stock price
            $stockPrice = StockPrice::where('symbol', $order->stock_symbol)->first();
            
            if (!$stockPrice) {
                $this->warn('Stock price not found for ' . $order->stock_symbol);
                continue;
            }

            $currentPrice = $stockPrice->price;

            // Check if order should be executed
            if ($this->shouldExecuteOrder($order, $currentPrice)) {
                try {
                    $this->executeOrder($order, $currentPrice, $settings);
                    $processedCount++;
                    $this->info('Executed order #' . $order->id . ' for ' . $order->stock_symbol . ' at $' . $currentPrice);
                } catch (\Exception $e) {
                    $this->error('Failed to execute order #' . $order->id . ': ' . $e->getMessage());
                }
            }
        }

        // Cancel expired orders
        $expiredOrders = StockTrade::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredOrders as $order) {
            DB::beginTransaction();
            try {
                // Refund reserved funds for buy orders
                if ($order->trade_type === 'buy') {
                    $user = User::find($order->user_id);
                    if ($user) {
                        $user->account_bal += $order->net_amount;
                        $user->save();
                    }
                }

                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Order expired',
                    'completed_at' => now(),
                ]);

                DB::commit();
                $cancelledCount++;
                $this->info('Cancelled expired order #' . $order->id);
            } catch (\Exception $e) {
                DB::rollback();
                $this->error('Failed to cancel expired order #' . $order->id . ': ' . $e->getMessage());
            }
        }

        $this->info('Processing complete: ' . $processedCount . ' executed, ' . $cancelledCount . ' cancelled');
        return 0;
    }

    /**
     * Determine if order should be executed based on current price
     */
    private function shouldExecuteOrder(StockTrade $order, float $currentPrice): bool
    {
        if ($order->order_type === StockTrade::ORDER_TYPE_LIMIT) {
            // Limit buy: execute when price drops to or below target
            // Limit sell: execute when price rises to or above target
            if ($order->trade_type === 'buy') {
                return $currentPrice <= $order->target_price;
            } else {
                return $currentPrice >= $order->target_price;
            }
        }

        if ($order->order_type === StockTrade::ORDER_TYPE_STOP) {
            // Stop buy: execute when price rises to or above trigger (buy higher)
            // Stop sell: execute when price drops to or below trigger (sell lower to prevent losses)
            if ($order->trade_type === 'buy') {
                return $currentPrice >= $order->trigger_price;
            } else {
                return $currentPrice <= $order->trigger_price;
            }
        }

        return false;
    }

    /**
     * Execute the order at current market price
     */
    private function executeOrder(StockTrade $order, float $executionPrice, Settings $settings): void
    {
        DB::transaction(function () use ($order, $executionPrice, $settings) {
            $user = User::find($order->user_id);
            
            if (!$user) {
                throw new \Exception('User not found');
            }

            // For sell orders, verify user still has sufficient shares
            if ($order->trade_type === 'sell') {
                $position = $this->calculateStockPosition($user->id, $order->stock_symbol);
                
                if ($position['quantity'] < $order->quantity) {
                    // Cancel order - user no longer has enough shares
                    $order->update([
                        'status' => 'cancelled',
                        'cancellation_reason' => 'Insufficient shares at execution time',
                        'completed_at' => now(),
                    ]);
                    $this->warn('Order #' . $order->id . ' cancelled - insufficient shares');
                    return;
                }
            }

            // Recalculate amounts based on actual execution price
            $totalAmount = $executionPrice * $order->quantity;
            $feeAmount = StockTrade::calculateFee($totalAmount, $settings->stock_trade_fee_percent);
            $netAmount = StockTrade::calculateNetAmount($totalAmount, $feeAmount, $order->trade_type);

            // Update order with execution details
            $order->update([
                'price' => $executionPrice,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $order->notes . ' | Auto-executed at $' . number_format($executionPrice, 2)
            ]);

            // For sell orders, credit the user's account and update ROI
            if ($order->trade_type === 'sell') {
                $position = $this->calculateStockPosition($user->id, $order->stock_symbol);
                $averageCost = $position['avg_cost'];
                $netProfit = ($executionPrice - $averageCost) * $order->quantity;
                
                $user->account_bal += $netAmount;
                $user->roi += $netProfit;
                $user->save();
            }
            // For buy orders, funds were already reserved when order was placed
            // No additional balance deduction needed

            $this->info('Executed ' . strtoupper($order->trade_type) . ' order #' . $order->id . ': ' . $order->quantity . ' shares of ' . $order->stock_symbol . ' at $' . $executionPrice);
        });
    }

    /**
     * Calculate user's position in a specific stock
     */
    private function calculateStockPosition($userId, $symbol)
    {
        $trades = StockTrade::where('user_id', $userId)
            ->where('stock_symbol', $symbol)
            ->where('status', 'completed')
            ->get();

        $quantity = 0;
        $totalCost = 0;

        foreach ($trades as $trade) {
            if ($trade->trade_type === 'buy') {
                $quantity += $trade->quantity;
                $totalCost += $trade->net_amount;
            } else {
                $quantity -= $trade->quantity;
                $totalCost -= $trade->net_amount;
            }
        }

        $averageCost = $quantity > 0 ? $totalCost / $quantity : 0;

        return [
            'quantity' => $quantity,
            'avg_cost' => $averageCost,
            'total_cost' => $totalCost,
        ];
    }
}
