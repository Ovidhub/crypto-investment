<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TradeController extends Controller
{
    /**
     * List of stablecoins to exclude from trading
     */
    private function getStablecoins()
    {
        return [
            'USDT', 'USDC', 'BUSD', 'DAI', 'TUSD', 'USDD', 'FRAX', 'LUSD',
            'USDP', 'GUSD', 'USDN', 'USTC', 'USDK', 'USDS', 'FDUSD', 'PYUSD',
            'SUSD', 'CUSD', 'OUSD', 'USDC.E', 'USDCE', 'USDT.E', 'USDTE',
            'TETHER', 'USD COIN', 'BINANCE USD', 'MULTI-COLLATERAL DAI',
            'TRUEUSD', 'FIRST DIGITAL USD', 'PAXOS STANDARD'
        ];
    }

    /**
     * Apply stablecoin filter to query
     */
    private function excludeStablecoins($query)
    {
        $stablecoins = $this->getStablecoins();

        return $query->where(function($q) use ($stablecoins) {
            foreach ($stablecoins as $stablecoin) {
                $q->where('symbol', '!=', $stablecoin)
                  ->where('name', 'NOT LIKE', "%{$stablecoin}%");
            }
        });
    }

    /**
     * Display the trading markets page with all instruments
     */
    public function index()
    {
        $instruments = $this->excludeStablecoins(Instrument::query())
            ->orderBy('volume', 'desc')
            ->orderBy('market_cap', 'desc')
            ->get();

        return view('user.trade.trade', [
            'title' => 'Trading Markets',
            'instruments' => $instruments
        ]);
    }

    /**
     * Display the trading interface for a specific instrument
     */
    public function single($id)
        {
            $instrument = Instrument::where('id', $id)->firstOrFail();
    
            // 1. Get Open Trades (active = 'yes')
            $openTrades = DB::table('user-plans')
                ->where('user', auth()->id())
                ->where('active', 'yes')
                ->where(function($query) use ($instrument) {
                    // Look inside the 'plan' column for the instrument name or symbol
                    $query->where('plan', $instrument->name)
                          ->orWhere('plan', $instrument->symbol);
                })
                ->orderBy('created_at', 'desc')
                ->get();
    
            // 2. Get Closed Trades (active = 'expired')
            $closedTrades = DB::table('user-plans')
                ->where('user', auth()->id())
                ->where('active', 'expired')
                ->where(function($query) use ($instrument) {
                    // Look inside the 'plan' column for the instrument name or symbol
                    $query->where('plan', $instrument->name)
                          ->orWhere('plan', $instrument->symbol);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
    
            return view('user.trade.single', [
                'title' => 'Trade ' . $instrument->name,
                'instrument' => $instrument,
                'openTrades' => $openTrades,
                'closedTrades' => $closedTrades
            ]);
        }
    /**
     * Get instruments by type via API
     */
    public function getByType($type)
    {
        $instruments = $this->excludeStablecoins(Instrument::where('type', $type))
            ->orderBy('volume', 'desc')
            ->orderBy('market_cap', 'desc')
            ->get();

        return response()->json($instruments);
    }

    /**
     * Search instruments
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type');

        $instruments = $this->excludeStablecoins(Instrument::query());

        if ($query) {
            $instruments->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('symbol', 'LIKE', "%{$query}%");
            });
        }

        if ($type && $type !== 'all') {
            $instruments->where('type', $type);
        }

        $results = $instruments->orderBy('volume', 'desc')
            ->orderBy('market_cap', 'desc')
            ->get();

        return response()->json($results);
    }

    /**
     * Display the trade monitoring page for a specific trade
     */
    public function monitor($tradeId)
    {
        $trade = DB::table('user-plans')
            ->where('id', $tradeId)
            ->where('user', auth()->id())
            ->first();

        if (!$trade) {
            return redirect()->route('trade.index')->with('error', 'Trade not found or you do not have permission to view it.');
        }

        // Get the instrument for additional market data
        $instrument = Instrument::where('symbol', $trade->assets)
            ->orWhere('name', $trade->assets)
            ->first();

        // Get related trades for this user and asset
        $relatedTrades = DB::table('user-plans')
            ->where('user', auth()->id())
            ->where('assets', $trade->assets)
            ->where('id', '!=', $tradeId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get user's trading statistics for this asset
        $stats = DB::table('user-plans')
            ->where('user', auth()->id())
            ->where('assets', $trade->assets)
            ->selectRaw('
                COUNT(*) as total_trades,
                COUNT(CASE WHEN active = "expired" THEN 1 END) as completed_trades,
                COUNT(CASE WHEN active = "yes" THEN 1 END) as active_trades,
                SUM(amount) as total_invested,
                AVG(amount) as avg_trade_size
            ')
            ->first();

        // Calculate P&L for this trade
        $pnl = $this->calculateTradesPnL($trade, $instrument);

        // Calculate time left for active trades
        $timeLeft = 'N/A';
        if ($trade->active === 'yes' && $trade->expire_date) {
            $now = Carbon::now();
            $expireDate = Carbon::parse($trade->expire_date);
            if ($now < $expireDate) {
                $timeLeft = $now->diffForHumans($expireDate);
            } else {
                $timeLeft = 'Expired';
            }
        }

        $title = 'Monitor Trade - ' . $trade->assets;

        return view('user.trade.monitor', compact('trade', 'instrument', 'relatedTrades', 'stats', 'pnl', 'timeLeft', 'title'));
    }

    /**
     * Calculate P&L for a trade based on current market data and trade status
     */
    private function calculateTradesPnL($trade, $instrument = null)
    {
        $pnl = [
            'current_value' => 0,
            'profit_loss' => 0,
            'return_percentage' => 0,
            'is_profit' => false,
            'status' => 'pending'
        ];

        if (!$trade) {
            return $pnl;
        }

        $tradeAmount = floatval($trade->amount);
        $leverage = floatval($trade->leverage ?? 1);

        if ($trade->active === 'expired') {
            // For completed trades, calculate based on result_type or use simulated data
            if (isset($trade->result_type)) {
                $isWin = $trade->result_type === 'WIN';
            } else {
                // Fallback: simulate based on trade data
                $isWin = $this->simulateTradeResult($trade);
            }

            if ($isWin) {
                // WIN: Calculate profit based on leverage (typical forex/crypto returns)
                $baseProfitRate = rand(3, 12); // 3-12% base return
                $leverageMultiplier = min($leverage / 10, 5); // Cap leverage effect
                $profitPercentage = $baseProfitRate * $leverageMultiplier;
                $profitPercentage = min($profitPercentage, 200); // Cap at 200%

                $pnl['profit_loss'] = ($tradeAmount * $profitPercentage) / 100;
                $pnl['is_profit'] = true;
                $pnl['status'] = 'completed_win';
            } else {
                // LOSE: Calculate loss with stop-loss protection
                if ($leverage >= 100) {
                    $lossPercentage = rand(60, 85);
                } elseif ($leverage >= 50) {
                    $lossPercentage = rand(45, 65);
                } elseif ($leverage >= 20) {
                    $lossPercentage = rand(30, 50);
                } else {
                    $lossPercentage = rand(15, 35);
                }

                $pnl['profit_loss'] = -($tradeAmount * $lossPercentage) / 100;
                $pnl['is_profit'] = false;
                $pnl['status'] = 'completed_loss';
            }

            $pnl['current_value'] = $tradeAmount + $pnl['profit_loss'];
            $pnl['return_percentage'] = ($pnl['profit_loss'] / $tradeAmount) * 100;

        } elseif ($trade->active === 'yes' && $instrument) {
            // For active trades, calculate current P&L based on market movement
            $entryPrice = floatval($trade->entry_price ?? $instrument->price);
            $currentPrice = floatval($instrument->price);

            if ($entryPrice > 0) {
                $priceChange = ($currentPrice - $entryPrice) / $entryPrice;

                // Apply trade direction
                if ($trade->type === 'Sell') {
                    $priceChange = -$priceChange;
                }

                // Apply leverage
                $leveragedChange = $priceChange * $leverage;

                // Calculate P&L
                $pnl['profit_loss'] = $tradeAmount * $leveragedChange;
                $pnl['current_value'] = $tradeAmount + $pnl['profit_loss'];
                $pnl['return_percentage'] = $leveragedChange * 100;
                $pnl['is_profit'] = $pnl['profit_loss'] > 0;
                $pnl['status'] = 'active';
            } else {
                // Fallback for active trades without entry price
                $pnl = $this->simulateActiveTradePnL($trade);
            }
        } else {
            // Unknown status
            $pnl['current_value'] = $tradeAmount;
            $pnl['status'] = 'unknown';
        }

        return $pnl;
    }

    /**
     * Simulate trade result for completed trades without result_type
     */
    private function simulateTradeResult($trade)
    {
        // Use trade ID and amount as seed for consistent results
        $seed = intval($trade->id) + intval($trade->amount * 100);
        mt_srand($seed);

        // Higher leverage = higher risk = lower win rate
        $leverage = floatval($trade->leverage ?? 1);
        if ($leverage >= 100) {
            $winRate = 0.3; // 30% win rate for high leverage
        } elseif ($leverage >= 50) {
            $winRate = 0.45; // 45% win rate
        } elseif ($leverage >= 20) {
            $winRate = 0.6; // 60% win rate
        } else {
            $winRate = 0.7; // 70% win rate for low leverage
        }

        return mt_rand() / mt_getrandmax() < $winRate;
    }

    /**
     * Simulate current P&L for active trades
     */
    private function simulateActiveTradePnL($trade)
    {
        $tradeAmount = floatval($trade->amount);
        $leverage = floatval($trade->leverage ?? 1);

        // Use trade ID as seed for consistent simulation
        mt_srand(intval($trade->id));

        // Simulate market movement (-5% to +5%)
        $marketMove = (mt_rand() / mt_getrandmax() - 0.5) * 0.1;

        // Apply leverage
        $leveragedMove = $marketMove * $leverage;

        $profitLoss = $tradeAmount * $leveragedMove;

        return [
            'current_value' => $tradeAmount + $profitLoss,
            'profit_loss' => $profitLoss,
            'return_percentage' => $leveragedMove * 100,
            'is_profit' => $profitLoss > 0,
            'status' => 'active'
        ];
    }
}
