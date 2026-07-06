<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TwelveDataService;
use App\Models\StockPrice;
use App\Models\StockTrade;
use App\Models\TradingWatchlist;
use App\Models\TradingWatchlistItem;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StockTradingController extends Controller
{
    protected $twelveDataService;

    public function __construct(TwelveDataService $twelveDataService)
    {
        $this->twelveDataService = $twelveDataService;
    }

    /**
     * Display stock trading index page
     */
    public function index()
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        // Check if stock trading is enabled
        if ($settings->enable_stock_trading !== 'on') {
            return redirect()->route('dashboard')
                ->with('error', 'Stock trading is currently unavailable');
        }

        // Get or create default stock watchlist
        $watchlist = TradingWatchlist::where('user_id', $user->id)
            ->where('type', 'stock')
            ->where('is_default', true)
            ->with('items')
            ->first();

        if (!$watchlist) {
            // Create default watchlist with popular stocks
            $watchlist = TradingWatchlist::create([
                'user_id' => $user->id,
                'name' => 'My Stocks',
                'type' => 'stock',
                'is_default' => true,
            ]);

            // Add default stocks
            $defaultStocks = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA'];
            foreach ($defaultStocks as $index => $symbol) {
                TradingWatchlistItem::create([
                    'watchlist_id' => $watchlist->id,
                    'symbol' => $symbol,
                    'market' => 'NASDAQ',
                    'sort_order' => $index,
                ]);
            }

            $watchlist->load('items');
        }

        // Get stock prices for watchlist items
        $symbols = $watchlist->items->pluck('symbol')->toArray();
        $stockPrices = StockPrice::whereIn('symbol', $symbols)->get()->keyBy('symbol');

        // Get user's recent trades
        $recentTrades = StockTrade::where('user_id', $user->id)
            ->with('stockPrice')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Calculate portfolio value
        $portfolioValue = $this->calculatePortfolioValue($user->id);
        
        // Calculate portfolio stats
        $totalInvested = StockTrade::where('user_id', $user->id)
            ->where('trade_type', 'buy')
            ->where('status', 'completed')
            ->sum('net_amount');
            
        $totalProfitLoss = $portfolioValue - $totalInvested;
        
        // Calculate overall portfolio ROI
        $portfolioROI = $totalInvested > 0 ? ($totalProfitLoss / $totalInvested) * 100 : 0;
        
        // Get all available stocks
        $allStocks = StockPrice::orderBy('symbol')->paginate(50);

        return view('user.stock-trading.index', [
            'settings' => $settings,
            'watchlist' => $watchlist->items()->with('stockPrice')->get(),
            'allStocks' => $allStocks,
            'stockPrices' => $stockPrices,
            'recentTrades' => $recentTrades,
            'portfolioValue' => $portfolioValue,
            'availableBalance' => $user->account_bal,
            'totalInvested' => $totalInvested,
            'totalProfitLoss' => $totalProfitLoss,
            'portfolioROI' => $portfolioROI,
            'maxWatchlistItems' => $settings->max_stock_watchlist_items ?? 50,
            'title' => 'Stock Trading',
        ]);
    }

    /**
     * Display user's stock portfolio/assets
     */
    public function portfolio()
    {
        $settings = Settings::first();
        $user = Auth::user();
        
        // Check if stock trading is enabled
        if ($settings->enable_stock_trading !== 'on') {
            return redirect()->route('dashboard')
                ->with('error', 'Stock trading is currently unavailable');
        }

        // Get all completed trades for this user
        $trades = StockTrade::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        // Calculate positions for each stock
        $positions = [];
        foreach ($trades as $trade) {
            $symbol = $trade->stock_symbol;
            
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
                $positions[$symbol]['total_cost'] -= $trade->net_amount;
            }
        }

        // Filter out positions with zero quantity and enrich with current data
        $assets = [];
        foreach ($positions as $symbol => $position) {
            if ($position['quantity'] > 0) {
                $stockPrice = StockPrice::where('symbol', $symbol)->first();
                
                if ($stockPrice) {
                    $avgCost = $position['total_cost'] / $position['quantity'];
                    $currentValue = $position['quantity'] * $stockPrice->price;
                    $profitLoss = $currentValue - $position['total_cost'];
                    $profitLossPercent = $position['total_cost'] > 0 ? ($profitLoss / $position['total_cost']) * 100 : 0;
                    
                    $assets[] = [
                        'symbol' => $symbol,
                        'name' => $stockPrice->name,
                        'exchange' => $stockPrice->exchange,
                        'logo_url' => $stockPrice->logo_url,
                        'quantity' => $position['quantity'],
                        'avg_cost' => $avgCost,
                        'current_price' => $stockPrice->price,
                        'total_cost' => $position['total_cost'],
                        'current_value' => $currentValue,
                        'profit_loss' => $profitLoss,
                        'profit_loss_percent' => $profitLossPercent,
                        'trades_count' => $position['trades_count'],
                        'price_change' => $stockPrice->change,
                        'percent_change' => $stockPrice->percent_change,
                    ];
                }
            }
        }

        // Sort by current value (largest holdings first)
        usort($assets, function($a, $b) {
            return $b['current_value'] <=> $a['current_value'];
        });

        // Calculate portfolio totals
        $totalInvested = array_sum(array_column($assets, 'total_cost'));
        $totalValue = array_sum(array_column($assets, 'current_value'));
        $totalProfitLoss = $totalValue - $totalInvested;
        $totalProfitLossPercent = $totalInvested > 0 ? ($totalProfitLoss / $totalInvested) * 100 : 0;

        return view('user.stock-trading.portfolio', [
            'settings' => $settings,
            'assets' => $assets,
            'totalInvested' => $totalInvested,
            'totalValue' => $totalValue,
            'totalProfitLoss' => $totalProfitLoss,
            'totalProfitLossPercent' => $totalProfitLossPercent,
            'availableBalance' => $user->account_bal,
            'title' => 'My Portfolio',
        ]);
    }

    /**
     * Display individual stock page
     */
    public function show($symbol)
    {
        $settings = Settings::first();
        $user = Auth::user();

        $symbol = strtoupper($symbol);
        
        // Get stock price from database
        $stockPrice = StockPrice::where('symbol', $symbol)->first();

        if (!$stockPrice) {
            // Fetch from API if not in database
            $quote = $this->twelveDataService->getQuote($symbol, true);
            
            if (isset($quote['error']) && $quote['error']) {
                return redirect()->route('stock-trading.index')
                    ->with('error', 'Stock symbol not found');
            }

            $stockPrice = StockPrice::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'name' => $quote['name'],
                    'exchange' => $quote['exchange'],
                    'logo_url' => $quote['logo_url'] ?? null,
                    'price' => $quote['price'],
                    'open' => $quote['open'],
                    'high' => $quote['high'],
                    'low' => $quote['low'],
                    'previous_close' => $quote['previous_close'],
                    'change' => $quote['change'],
                    'percent_change' => $quote['percent_change'],
                    'volume' => $quote['volume'],
                    'last_updated' => now(),
                ]
            );
        }

        // Get user's trades for this stock
        $userTrades = StockTrade::where('user_id', $user->id)
            ->where('stock_symbol', $symbol)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's pending orders for this stock
        $pendingOrders = StockTrade::where('user_id', $user->id)
            ->where('stock_symbol', $symbol)
            ->where('status', 'pending')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate user's position in this stock
        $position = $this->calculateStockPosition($user->id, $symbol);

        // Calculate ROI percentage for this stock
        $roi = $position && $position['total_cost'] > 0 
            ? ($position['profit_loss'] / $position['total_cost']) * 100 
            : 0;

        return view('user.stock-trading.show', [
            'settings' => $settings,
            'stock' => $stockPrice,
            'userTrades' => $userTrades,
            'pendingOrders' => $pendingOrders,
            'position' => $position,
            'availableBalance' => $user->account_bal,
            'feePercent' => $settings->stock_trade_fee_percent,
            'roi' => $roi,
            'title' => $stockPrice->name . ' (' . $symbol . ')',
        ]);
    }

    /**
     * Place order (market, limit, or stop)
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
            'trade_type' => 'required|in:buy,sell',
            'order_type' => 'required|in:market,limit,stop',
            'quantity' => 'required|numeric|min:0.00000001',
            'target_price' => 'required_if:order_type,limit,stop|nullable|numeric|min:0.01',
            'trigger_price' => 'required_if:order_type,stop|nullable|numeric|min:0.01',
            'expiry_type' => 'required|in:gtc,day,date',
            'expires_at' => 'required_if:expiry_type,date|nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $user = Auth::user();
        $settings = Settings::first();
        $symbol = strtoupper($request->symbol);
        $quantity = floatval($request->quantity);
        $orderType = $request->order_type;
        $tradeType = $request->trade_type;

        // Get current stock price
        $stockPrice = StockPrice::where('symbol', $symbol)->first();
        
        if (!$stockPrice) {
            return back()->with('error', 'Stock not found');
        }

        // For market orders, execute immediately using existing methods
        if ($orderType === StockTrade::ORDER_TYPE_MARKET) {
            if ($tradeType === 'buy') {
                return $this->buyStock($request);
            } else {
                return $this->sellStock($request);
            }
        }

        // For limit and stop orders, create pending order
        $feePercent = $settings->stock_trade_fee_percent;
        
        // Use target/trigger price for calculation estimate
        $estimatedPrice = $orderType === StockTrade::ORDER_TYPE_LIMIT
            ? floatval($request->target_price)
            : floatval($request->trigger_price);
        
        $totalAmount = $estimatedPrice * $quantity;
        $feeAmount = StockTrade::calculateFee($totalAmount, $feePercent);
        $netAmount = StockTrade::calculateNetAmount($totalAmount, $feeAmount, $tradeType);

        // For buy orders, check and reserve funds
        if ($tradeType === 'buy') {
            if ($user->account_bal < $netAmount) {
                return back()->with('error', 'Insufficient balance. Required: ' . $settings->currency . number_format($netAmount, 2));
            }
        }

        // For sell orders, check if user has sufficient shares
        if ($tradeType === 'sell') {
            $position = $this->calculateStockPosition($user->id, $symbol);
            if ($position['quantity'] < $quantity) {
                return back()->with('error', 'Insufficient shares. You own ' . $position['quantity'] . ' shares.');
            }
        }

        // Calculate expiry date
        $expiresAt = null;
        if ($request->expiry_type === 'day') {
            // Day order expires at market close (4 PM ET)
            $expiresAt = now()->setTime(16, 0, 0);
            if ($expiresAt->isPast()) {
                $expiresAt->addDay();
            }
        } elseif ($request->expiry_type === 'date') {
            $expiresAt = $request->expires_at;
        }

        DB::beginTransaction();
        try {
            // Reserve funds for buy orders
            if ($tradeType === 'buy') {
                $user->account_bal -= $netAmount;
                $user->save();
            }

            // Create pending order
            $trade = StockTrade::create([
                'user_id' => $user->id,
                'stock_symbol' => $symbol,
                'trade_type' => $tradeType,
                'order_type' => $orderType,
                'quantity' => $quantity,
                'price' => $estimatedPrice,
                'target_price' => $request->target_price,
                'trigger_price' => $request->trigger_price,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'fee_percent' => $feePercent,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'expiry_type' => $request->expiry_type,
                'expires_at' => $expiresAt,
                'notes' => ucfirst($orderType) . ' order placed at ' . $settings->currency . number_format($estimatedPrice, 2),
            ]);

            DB::commit();

            $orderTypeLabel = ucfirst($orderType);
            return back()->with('success', $orderTypeLabel . ' order placed: ' . strtoupper($tradeType) . ' ' . $quantity . ' shares of ' . $symbol . ' at ' . $settings->currency . number_format($estimatedPrice, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    /**
     * Cancel pending order
     */
    public function cancelOrder($orderId)
    {
        $user = Auth::user();
        
        $order = StockTrade::where('id', $orderId)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Order not found or already processed'], 404);
            }
            return back()->with('error', 'Order not found or already processed');
        }

        DB::beginTransaction();
        try {
            // Refund reserved funds for buy orders
            if ($order->trade_type === 'buy') {
                $user->account_bal += $order->net_amount;
                $user->save();
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => 'Cancelled by user',
                'completed_at' => now(),
            ]);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Order cancelled successfully']);
            }
            return back()->with('success', 'Order cancelled successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Cancellation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user's open (pending) orders
     */
    public function getOpenOrders($symbol = null)
    {
        $user = Auth::user();
        
        $query = StockTrade::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->with('stockPrice');

        if ($symbol) {
            $query->where('stock_symbol', strtoupper($symbol));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'orders' => $orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'symbol' => $order->stock_symbol,
                        'trade_type' => $order->trade_type,
                        'order_type' => $order->order_type,
                        'quantity' => $order->quantity,
                        'target_price' => $order->target_price,
                        'trigger_price' => $order->trigger_price,
                        'status' => $order->status,
                        'expiry_type' => $order->expiry_type,
                        'expires_at' => $order->expires_at,
                        'created_at' => $order->created_at->format('M d, Y H:i'),
                        'current_price' => $order->stockPrice ? $order->stockPrice->price : null,
                    ];
                })
            ]);
        }

        return view('user.stock-trading.open-orders', compact('orders'));
    }

    /**
     * Buy stock
     */
    public function buyStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
            'quantity' => 'required|numeric|min:0.00000001',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $user = Auth::user();
        $settings = Settings::first();
        $symbol = strtoupper($request->symbol);
        $quantity = floatval($request->quantity);

        // Get current stock price
        $stockPrice = StockPrice::where('symbol', $symbol)->first();
        
        if (!$stockPrice) {
            return back()->with('error', 'Stock not found');
        }

        // Calculate amounts
        $price = $stockPrice->price;
        $totalAmount = $price * $quantity;
        $feePercent = $settings->stock_trade_fee_percent;
        $feeAmount = StockTrade::calculateFee($totalAmount, $feePercent);
        $netAmount = StockTrade::calculateNetAmount($totalAmount, $feeAmount, 'buy');

        // Check if user has sufficient balance
        if ($user->account_bal < $netAmount) {
            return back()->with('error', 'Insufficient balance. Required: ' . $settings->currency . number_format($netAmount, 2));
        }

        DB::beginTransaction();
        try {
            // Deduct from user balance
            $user->account_bal -= $netAmount;
            $user->save();

            // Create trade record (auto-completed, no admin approval needed)
            $trade = StockTrade::create([
                'user_id' => $user->id,
                'stock_symbol' => $symbol,
                'trade_type' => 'buy',
                'quantity' => $quantity,
                'price' => $price,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'fee_percent' => $feePercent,
                'net_amount' => $netAmount,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Successfully bought ' . $quantity . ' shares of ' . $symbol . ' for ' . $settings->currency . number_format($netAmount, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    /**
     * Sell stock
     */
    public function sellStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
            'quantity' => 'required|numeric|min:0.00000001',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $user = Auth::user();
        $settings = Settings::first();
        $symbol = strtoupper($request->symbol);
        $quantity = floatval($request->quantity);

        // Check if user has sufficient shares
        $position = $this->calculateStockPosition($user->id, $symbol);
        
        if ($position['quantity'] < $quantity) {
            return back()->with('error', 'Insufficient shares. You own ' . $position['quantity'] . ' shares.');
        }

        // Get current stock price
        $stockPrice = StockPrice::where('symbol', $symbol)->first();
        
        if (!$stockPrice) {
            return back()->with('error', 'Stock not found');
        }

        // Calculate amounts
        $price = $stockPrice->price;
        $totalAmount = $price * $quantity;
        $feePercent = $settings->stock_trade_fee_percent;
        $feeAmount = StockTrade::calculateFee($totalAmount, $feePercent);
        $netAmount = StockTrade::calculateNetAmount($totalAmount, $feeAmount, 'sell');

        // Calculate net profit for ROI tracking
        // Profit = (sale price - average cost) × quantity sold
        $averageCost = $position['avg_cost'];
        $netProfit = ($price - $averageCost) * $quantity;

        DB::beginTransaction();
        try {
            // Add to user balance
            $user->account_bal += $netAmount;
            
            // Add net profit to user's ROI (tracks all profits made in the system)
            $user->roi += $netProfit;

            $user->save();

            // Create trade record (auto-completed, no admin approval needed)
            $trade = StockTrade::create([
                'user_id' => $user->id,
                'stock_symbol' => $symbol,
                'trade_type' => 'sell',
                'quantity' => $quantity,
                'price' => $price,
                'total_amount' => $totalAmount,
                'fee_amount' => $feeAmount,
                'fee_percent' => $feePercent,
                'net_amount' => $netAmount,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Successfully sold ' . $quantity . ' shares of ' . $symbol . ' for ' . $settings->currency . number_format($netAmount, 2));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }

    /**
     * Add stock to watchlist
     */
    public function addToWatchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
        ]);

        if ($validator->fails()) {
            // Check if request expects JSON
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
            }
            return back()->with('error', $validator->errors()->first());
        }

        $user = Auth::user();
        $settings = Settings::first();
        $symbol = strtoupper($request->symbol);

        // Get or create default watchlist
        $watchlist = TradingWatchlist::firstOrCreate(
            [
                'user_id' => $user->id,
                'type' => 'stock',
                'is_default' => true,
            ],
            [
                'name' => 'My Stocks',
            ]
        );

        // Check watchlist item limit
        $currentCount = TradingWatchlistItem::where('watchlist_id', $watchlist->id)->count();
        $maxItems = $settings->max_stock_watchlist_items;

        if ($currentCount >= $maxItems) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Watchlist limit reached (max ' . $maxItems . ' stocks)'], 400);
            }
            return back()->with('error', 'Watchlist limit reached (max ' . $maxItems . ' stocks)');
        }

        // Check if already in watchlist
        $exists = TradingWatchlistItem::where('watchlist_id', $watchlist->id)
            ->where('symbol', $symbol)
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Stock already in watchlist'], 400);
            }
            return back()->with('error', 'Stock already in watchlist');
        }

        // Add to watchlist
        TradingWatchlistItem::create([
            'watchlist_id' => $watchlist->id,
            'symbol' => $symbol,
            'sort_order' => $currentCount,
        ]);

        // Fetch stock data if not in database (initial fetch with logo)
        $stockPrice = StockPrice::where('symbol', $symbol)->first();
        
        if (!$stockPrice) {
            $quote = $this->twelveDataService->getQuote($symbol, true);
            
            if (!isset($quote['error'])) {
                StockPrice::create([
                    'symbol' => $symbol,
                    'name' => $quote['name'],
                    'exchange' => $quote['exchange'],
                    'logo_url' => $quote['logo_url'] ?? null,
                    'price' => $quote['price'],
                    'open' => $quote['open'],
                    'high' => $quote['high'],
                    'low' => $quote['low'],
                    'previous_close' => $quote['previous_close'],
                    'change' => $quote['change'],
                    'percent_change' => $quote['percent_change'],
                    'volume' => $quote['volume'],
                    'last_updated' => now(),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Stock added to watchlist']);
        }
        return back()->with('success', 'Stock added to watchlist');
    }

    /**
     * Remove stock from watchlist
     */
    public function removeFromWatchlist(Request $request, $symbol)
    {
        $user = Auth::user();
        $symbol = strtoupper($symbol);

        $watchlist = TradingWatchlist::where('user_id', $user->id)
            ->where('type', 'stock')
            ->where('is_default', true)
            ->first();

        if (!$watchlist) {
            return response()->json(['success' => false, 'message' => 'Watchlist not found'], 404);
        }

        $item = TradingWatchlistItem::where('watchlist_id', $watchlist->id)
            ->where('symbol', $symbol)
            ->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Stock not in watchlist'], 404);
        }

        $item->delete();

        return response()->json(['success' => true, 'message' => 'Stock removed from watchlist']);
    }

    /**
     * Search stocks
     */
    public function searchStocks(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = $this->twelveDataService->searchSymbol($query);

        return response()->json(['results' => $results]);
    }

    /**
     * Get stock quote (API endpoint for AJAX)
     */
    public function getQuote($symbol)
    {
        $symbol = strtoupper($symbol);
        
        // Get from database first
        $stockPrice = StockPrice::where('symbol', $symbol)->first();

        if ($stockPrice && $stockPrice->last_updated && $stockPrice->last_updated->diffInMinutes(now()) < 5) {
            // Data is fresh (less than 5 minutes old)
            return response()->json($stockPrice);
        }

        // Fetch fresh data from API
        $quote = $this->twelveDataService->getQuote($symbol, false);

        if (!isset($quote['error'])) {
            $stockPrice = StockPrice::updateOrCreate(
                ['symbol' => $symbol],
                [
                    'name' => $quote['name'],
                    'exchange' => $quote['exchange'],
                    'price' => $quote['price'],
                    'open' => $quote['open'],
                    'high' => $quote['high'],
                    'low' => $quote['low'],
                    'previous_close' => $quote['previous_close'],
                    'change' => $quote['change'],
                    'percent_change' => $quote['percent_change'],
                    'volume' => $quote['volume'],
                    'last_updated' => now(),
                ]
            );

            return response()->json($stockPrice);
        }

        return response()->json(['error' => 'Failed to fetch quote'], 500);
    }

    /**
     * Calculate user's total portfolio value
     */
    private function calculatePortfolioValue($userId)
    {
        $trades = StockTrade::where('user_id', $userId)
            ->completed()
            ->get();

        $positions = [];

        foreach ($trades as $trade) {
            if (!isset($positions[$trade->stock_symbol])) {
                $positions[$trade->stock_symbol] = 0;
            }

            if ($trade->trade_type === 'buy') {
                $positions[$trade->stock_symbol] += $trade->quantity;
            } else {
                $positions[$trade->stock_symbol] -= $trade->quantity;
            }
        }

        $totalValue = 0;

        foreach ($positions as $symbol => $quantity) {
            if ($quantity > 0) {
                $stockPrice = StockPrice::where('symbol', $symbol)->first();
                if ($stockPrice) {
                    $totalValue += $quantity * $stockPrice->price;
                }
            }
        }

        return $totalValue;
    }

    /**
     * Calculate user's position in a specific stock
     */
    private function calculateStockPosition($userId, $symbol)
    {
        $trades = StockTrade::where('user_id', $userId)
            ->where('stock_symbol', $symbol)
            ->completed()
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

        // Get current stock price
        $stockPrice = StockPrice::where('symbol', $symbol)->first();
        $currentPrice = $stockPrice ? $stockPrice->price : 0;

        // Calculate values
        $averageCost = $quantity > 0 ? $totalCost / $quantity : 0;
        $currentValue = $quantity * $currentPrice;
        $profitLoss = $currentValue - $totalCost;
        $profitLossPercent = $totalCost > 0 ? ($profitLoss / $totalCost) * 100 : 0;

        return [
            'quantity' => $quantity,
            'avg_cost' => $averageCost,
            'average_price' => $averageCost, // Keep for backward compatibility
            'total_cost' => $totalCost,
            'current_value' => $currentValue,
            'profit_loss' => $profitLoss,
            'profit_loss_percent' => $profitLossPercent,
        ];
    }
}
