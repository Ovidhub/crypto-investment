<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CryptoPrice;
use App\Models\CryptoTrade;
use App\Models\TradingWatchlist;
use App\Models\TradingWatchlistItem;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CryptoTradingController extends Controller
{
    /**
     * Display crypto trading dashboard
     */
    public function index()
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        // Check if crypto trading is enabled
        if ($settings->enable_crypto_trading !== 'on') {
            return redirect()->route('dashboard')
                ->with('error', 'Crypto trading is currently unavailable');
        }

        // Get or create default crypto watchlist
        $watchlist = TradingWatchlist::where('user_id', $user->id)
            ->where('type', 'crypto')
            ->where('is_default', true)
            ->with('items')
            ->first();

        if (!$watchlist) {
            // Create default watchlist with popular cryptos
            $watchlist = TradingWatchlist::create([
                'user_id' => $user->id,
                'name' => 'My Cryptos',
                'type' => 'crypto',
                'is_default' => true,
            ]);

            // Add default cryptos
            $defaultCryptos = ['BTC', 'ETH', 'BNB', 'XRP', 'ADA'];
            foreach ($defaultCryptos as $index => $symbol) {
                TradingWatchlistItem::create([
                    'watchlist_id' => $watchlist->id,
                    'symbol' => $symbol,
                    'market' => 'CRYPTO',
                    'sort_order' => $index,
                ]);
            }

            $watchlist->load('items');
        }

        // Get crypto prices for watchlist items
        $symbols = $watchlist->items->pluck('symbol')->toArray();
        $cryptoPrices = CryptoPrice::whereIn('coin_symbol', $symbols)->get()->keyBy('coin_symbol');

        // Get user's recent trades
        $recentTrades = CryptoTrade::where('user_id', $user->id)
            ->with('cryptoPrice')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Calculate portfolio value
        $portfolioValue = $this->calculatePortfolioValue($user->id);
        
        // Calculate portfolio stats
        $totalInvested = CryptoTrade::where('user_id', $user->id)
            ->where('trade_type', 'buy')
            ->where('status', 'completed')
             ->sum('net_amount');
            
        $totalProfitLoss = $portfolioValue - $totalInvested;
        
        // Calculate overall portfolio ROI
        $portfolioROI = $totalInvested > 0 ? ($totalProfitLoss / $totalInvested) * 100 : 0;
        
        // Get all available cryptos
        $cryptos = CryptoPrice::orderBy('coin_symbol')->paginate(50);

        // Get pending orders count
        $pendingOrders = CryptoTrade::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

          

        return view('user.crypto-trading.index', [
            'settings' => $settings,
            'watchlist' => $watchlist->items()->with('cryptoPrice')->get(),
            'cryptos' => $cryptos,
            'cryptoPrices' => $cryptoPrices,
            'recentTrades' => $recentTrades,
            'portfolioValue' => $portfolioValue,
            'availableBalance' => $user->account_bal,
            'totalInvested' => $totalInvested,
            'totalProfitLoss' => $totalProfitLoss,
            'portfolioROI' => $portfolioROI,
            'pendingOrders' => $pendingOrders,
            'maxWatchlistItems' => $settings->max_crypto_watchlist_items ?? 50,
            'title' => 'Crypto Trading',
        ]);
    }

    /**
     * Calculate portfolio value
     */
    private function calculatePortfolioValue($userId)
    {
        $trades = CryptoTrade::where('user_id', $userId)
            ->where('status', 'completed')
            ->get();

        $positions = [];
        foreach ($trades as $trade) {
            $symbol = $trade->coin_symbol;
            
            if (!isset($positions[$symbol])) {
                $positions[$symbol] = 0;
            }

            if ($trade->trade_type === 'buy') {
                $positions[$symbol] += $trade->quantity;
            } else {
                $positions[$symbol] -= $trade->quantity;
            }
        }

        $totalValue = 0;
        foreach ($positions as $symbol => $quantity) {
            if ($quantity > 0) {
                $crypto = CryptoPrice::where('coin_symbol', $symbol)->first();
                if ($crypto) {
                    $totalValue += $quantity * $crypto->price_usd;
                }
            }
        }

        return $totalValue;
    }

    /**
     * Display trading terminal for specific crypto
     */
    public function trade($symbol)
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        if ($settings->enable_crypto_trading !== 'on') {
            return redirect()->route('dashboard')
                ->with('error', 'Crypto trading is currently unavailable');
        }

        $crypto = CryptoPrice::where('coin_symbol', strtoupper($symbol))->firstOrFail();
        
        // Get user's position in this crypto
        $trades = CryptoTrade::where('user_id', $user->id)
            ->where('coin_symbol', $crypto->coin_symbol)
            ->where('status', 'completed')
            ->get();

        $position = 0;
        $avgCost = 0;
        $totalCost = 0;

        foreach ($trades as $trade) {
            if ($trade->trade_type === 'buy') {
                $position += $trade->quantity;
                $totalCost += $trade->total_amount;
            } else {
                $position -= $trade->quantity;
            }
        }

        if ($position > 0) {
            $avgCost = $totalCost / $position;
        }

        // Get pending orders for this crypto
        $pendingOrders = CryptoTrade::where('user_id', $user->id)
            ->where('coin_symbol', $crypto->coin_symbol)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent trades for this crypto
        $marketTrades = CryptoTrade::where('coin_symbol', $crypto->coin_symbol)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(20)
            ->get();

        return view('user.crypto-trading.trade', [
            'settings' => $settings,
            'crypto' => $crypto,
            'position' => $position,
            'avgCost' => $avgCost,
            'availableBalance' => $user->account_bal,
            'pendingOrders' => $pendingOrders,
            'marketTrades' => $marketTrades,
            'title' => $crypto->name . ' Trading',
        ]);
    }

    /**
     * Display user's crypto portfolio
     */
    public function portfolio()
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        if ($settings->enable_crypto_trading !== 'on') {
            return redirect()->route('dashboard')
                ->with('error', 'Crypto trading is currently unavailable');
        }

        // Get all completed trades
        $trades = CryptoTrade::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        // Calculate positions for each crypto
        $positions = [];
        foreach ($trades as $trade) {
            $symbol = $trade->coin_symbol;
            
            if (!isset($positions[$symbol])) {
                $positions[$symbol] = [
                    'symbol' => $symbol,
                    'quantity' => 0,
                    'total_cost' => 0,
                    'trades_count' => 0,
                ];
            }

            $positions[$symbol]['trades_count']++;

            if ($trade->trade_type === 'buy') {
                $positions[$symbol]['quantity'] += $trade->quantity;
                $positions[$symbol]['total_cost'] += $trade->net_amount;
            } else {
                $positions[$symbol]['quantity'] -= $trade->quantity;
            }
        }

        // Filter out zero positions and add current prices
        $holdings = [];
        $totalPortfolioValue = 0;

        foreach ($positions as $symbol => $position) {
            if ($position['quantity'] > 0) {
                $crypto = CryptoPrice::where('coin_symbol', $symbol)->first();
                
                if ($crypto) {
                    $currentValue = $position['quantity'] * $crypto->price_usd;
                    $avgCost = $position['total_cost'] / $position['quantity'];
                    $profitLoss = $currentValue - $position['total_cost'];
                    $profitLossPercent = ($position['total_cost'] > 0) 
                        ? ($profitLoss / $position['total_cost']) * 100 
                        : 0;

                    $holdings[] = [
                        'crypto' => $crypto,
                        'quantity' => $position['quantity'],
                        'avg_cost' => $avgCost,
                        'total_cost' => $position['total_cost'],
                        'current_value' => $currentValue,
                        'profit_loss' => $profitLoss,
                        'profit_loss_percent' => $profitLossPercent,
                        'trades_count' => $position['trades_count'],
                    ];

                    $totalPortfolioValue += $currentValue;
                }
            }
        }

        // Calculate concentration percentages
        foreach ($holdings as &$holding) {
            $holding['concentration'] = ($totalPortfolioValue > 0) 
                ? ($holding['current_value'] / $totalPortfolioValue) * 100 
                : 0;
        }

        // Sort by current value descending
        usort($holdings, function($a, $b) {
            return $b['current_value'] <=> $a['current_value'];
        });

        $totalInvested = array_sum(array_column($holdings, 'total_cost'));
        $totalProfitLoss = $totalPortfolioValue - $totalInvested;

        return view('user.crypto-trading.portfolio', [
            'settings' => $settings,
            'holdings' => $holdings,
            'totalPortfolioValue' => $totalPortfolioValue,
            'totalInvested' => $totalInvested,
            'totalProfitLoss' => $totalProfitLoss,
            'holdingsCount' => count($holdings),
            'title' => 'My Crypto Portfolio',
        ]);
    }

    /**
     * Display order history
     */
    public function orders()
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        if ($settings->enable_crypto_trading !== 'on') {
            return redirect()->route('dashboard')
                ->with('error', 'Crypto trading is currently unavailable');
        }

        $activeOrders = CryptoTrade::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('cryptoPrice')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'active_page');

        $completedOrders = CryptoTrade::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('cryptoPrice')
            ->orderBy('completed_at', 'desc')
            ->paginate(20, ['*'], 'completed_page');

        $cancelledOrders = CryptoTrade::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->with('cryptoPrice')
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'cancelled_page');

        return view('user.crypto-trading.orders', [
            'settings' => $settings,
            'activeOrders' => $activeOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'title' => 'My Orders',
        ]);
    }

    /**
     * Place a new order
     */
    public function placeOrder(Request $request)
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        if ($settings->enable_crypto_trading !== 'on') {
            return back()->with('error', 'Crypto trading is currently unavailable');
        }

        $validator = Validator::make($request->all(), [
            'coin_symbol' => 'required|exists:crypto_prices,coin_symbol',
            'trade_type' => 'required|in:buy,sell',
            'order_type' => 'required|in:market,limit,stop_loss',
            'quantity' => 'required|numeric|min:0.00000001',
            'limit_price' => 'required_if:order_type,limit|nullable|numeric|min:0',
            'stop_price' => 'required_if:order_type,stop_loss|nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $crypto = CryptoPrice::where('coin_symbol', $request->coin_symbol)->firstOrFail();
        
        // Determine price based on order type
        if ($request->order_type === 'market') {
            $price = $crypto->price_usd;
        } elseif ($request->order_type === 'limit') {
            $price = $request->limit_price;
        } else { // stop_loss
            $price = $request->stop_price;
        }
        
        // Calculate amounts
        $totalAmount = $request->quantity * $price;
        $feePercent = $settings->crypto_trade_fee_percent ?? 0.25;
        $feeAmount = ($totalAmount * $feePercent) / 100;
        $netAmount = $totalAmount + $feeAmount;

        // For buy orders, check balance
        if ($request->trade_type === 'buy') {
            if ($user->account_bal < $netAmount) {
                return back()->with('error', 'Insufficient balance');
            }
        }

        // For sell orders, check if user has enough crypto
        if ($request->trade_type === 'sell') {
            $position = CryptoTrade::where('user_id', $user->id)
                ->where('coin_symbol', $request->coin_symbol)
                ->where('status', 'completed')
                ->get()
                ->sum(function($trade) {
                    return $trade->trade_type === 'buy' ? $trade->quantity : -$trade->quantity;
                });

            if ($position < $request->quantity) {
                return back()->with('error', 'Insufficient crypto balance');
            }
        }

        DB::beginTransaction();
        try {
            // Determine status (market orders execute immediately)
            $status = $request->order_type === 'market' ? 'completed' : 'pending';
            $completedAt = $status === 'completed' ? now() : null;

            // Create trade
            $trade = CryptoTrade::create([
                'user_id' => $user->id,
                'coin_symbol' => $request->coin_symbol,
                'trade_type' => $request->trade_type,
                'quantity' => $request->quantity,
                'price_usd' => $price,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'fee_percent' => $feePercent,
                'net_amount' => $netAmount,
                'status' => $status,
                'order_type' => $request->order_type,
                'limit_price' => $request->limit_price,
                'stop_price' => $request->stop_price,
                'completed_at' => $completedAt,
            ]);

            // Update user balance for completed orders
            if ($status === 'completed') {
                if ($request->trade_type === 'buy') {
                    $user->decrement('account_bal', $netAmount);
                } else {
                    // For sell orders, calculate profit/loss
                    // Get all buy trades to calculate average cost basis
                    $buyTrades = CryptoTrade::where('user_id', $user->id)
                        ->where('coin_symbol', $request->coin_symbol)
                        ->where('trade_type', 'buy')
                        ->where('status', 'completed')
                        ->get();
                    
                    $totalBuyQuantity = $buyTrades->sum('quantity');
                    $totalBuyCost = $buyTrades->sum('net_amount'); // Total spent including fees
                    
                    if ($totalBuyQuantity > 0) {
                        $avgCostBasis = $totalBuyCost / $totalBuyQuantity;
                        $costOfSoldCoins = $avgCostBasis * $request->quantity;
                        $saleProceeds = $totalAmount - $feeAmount; // What user receives
                        $profitLoss = $saleProceeds - $costOfSoldCoins;
                        
                        // Add profit/loss to user's ROI
                        $user->increment('roi', $profitLoss);
                    }
                    
                    $user->increment('account_bal', $totalAmount - $feeAmount);
                }

                // Create transaction record
                Tp_Transaction::create([
                    'user' => $user->id,
                    'plan' => 'Crypto Trading: ' . $crypto->name,
                    'amount' => $totalAmount,
                    'type' => $request->trade_type === 'buy' ? 'Debit' : 'Credit',
                ]);
            }

            DB::commit();

            $message = $status === 'completed' 
                ? ucfirst($request->trade_type) . ' order executed successfully'
                : ucfirst($request->order_type) . ' order placed successfully';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a pending order
     */
    public function cancelOrder($orderId)
    {
        $user = Auth::user();
        
        $order = CryptoTrade::where('id', $orderId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully');
    }

    /**
     * Add crypto to watchlist
     */
    public function addToWatchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|exists:crypto_prices,coin_symbol',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();
        $watchlist = TradingWatchlist::where('user_id', $user->id)
            ->where('type', 'crypto')
            ->where('is_default', true)
            ->firstOrFail();

        // Check if already in watchlist
        $exists = TradingWatchlistItem::where('watchlist_id', $watchlist->id)
            ->where('symbol', $request->symbol)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Already in watchlist');
        }

        TradingWatchlistItem::create([
            'watchlist_id' => $watchlist->id,
            'symbol' => $request->symbol,
            'market' => 'CRYPTO',
            'sort_order' => $watchlist->items()->count(),
        ]);

        return back()->with('success', 'Added to watchlist');
    }

    /**
     * Remove crypto from watchlist
     */
    public function removeFromWatchlist($symbol)
    {
        $user = Auth::user();
        $watchlist = TradingWatchlist::where('user_id', $user->id)
            ->where('type', 'crypto')
            ->where('is_default', true)
            ->firstOrFail();

        TradingWatchlistItem::where('watchlist_id', $watchlist->id)
            ->where('symbol', $symbol)
            ->delete();

        return back()->with('success', 'Removed from watchlist');
    }
}
