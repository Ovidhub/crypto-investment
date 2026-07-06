<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTrade;
use App\Models\StockPrice;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StockTradingController extends Controller
{
    /**
     * Display all stock trades
     */
    public function index()
    {
        $trades = StockTrade::with(['user', 'stockPrice'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_trades' => StockTrade::count(),
            'total_buy_trades' => StockTrade::buys()->count(),
            'total_sell_trades' => StockTrade::sells()->count(),
            'total_completed' => StockTrade::completed()->count(),
            'total_volume' => StockTrade::completed()->sum('total_amount'),
            'total_fees_collected' => StockTrade::completed()->sum('fee_amount'),
        ];

        return view('admin.stock-trading.index', [
            'trades' => $trades,
            'stats' => $stats,
            'title' => 'Stock Trades',
        ]);
    }

    /**
     * Display trades for specific user
     */
    public function userTrades($userId)
    {
        $user = User::findOrFail($userId);
        
        $trades = StockTrade::where('user_id', $userId)
            ->with('stockPrice')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Calculate portfolio stats
        $totalTrades = StockTrade::where('user_id', $userId)->count();
        $buyTrades = StockTrade::where('user_id', $userId)->buys()->completed()->count();
        $sellTrades = StockTrade::where('user_id', $userId)->sells()->completed()->count();
        $totalInvested = StockTrade::where('user_id', $userId)->buys()->completed()->sum('net_amount');
        $portfolioValue = $this->calculateUserPortfolioValue($userId);
        $totalProfitLoss = $portfolioValue - $totalInvested;

        $stats = [
            'total_trades' => $totalTrades,
            'total_bought' => StockTrade::where('user_id', $userId)->buys()->completed()->sum('quantity'),
            'total_sold' => StockTrade::where('user_id', $userId)->sells()->completed()->sum('quantity'),
            'total_spent' => $totalInvested,
            'total_received' => StockTrade::where('user_id', $userId)->sells()->completed()->sum('net_amount'),
            'total_fees_paid' => StockTrade::where('user_id', $userId)->completed()->sum('fee_amount'),
        ];

        return view('admin.stock-trading.user-trades', [
            'user' => $user,
            'trades' => $trades,
            'stats' => $stats,
            'totalTrades' => $totalTrades,
            'buyTrades' => $buyTrades,
            'sellTrades' => $sellTrades,
            'portfolioValue' => $portfolioValue,
            'totalInvested' => $totalInvested,
            'totalProfitLoss' => $totalProfitLoss,
            'title' => 'User Stock Trades - ' . $user->name,
        ]);
    }

    /**
     * Calculate user's portfolio value
     */
    private function calculateUserPortfolioValue($userId)
    {
        $trades = StockTrade::where('user_id', $userId)
            ->where('status', 'completed')
            ->get();

        $positions = [];
        foreach ($trades as $trade) {
            $symbol = $trade->stock_symbol;
            
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
                $stockPrice = StockPrice::where('symbol', $symbol)->first();
                if ($stockPrice) {
                    $totalValue += $quantity * $stockPrice->price;
                }
            }
        }

        return $totalValue;
    }

    /**
     * View user's detailed holdings/positions
     */
    public function userHoldings($userId)
    {
        $user = User::findOrFail($userId);
        
        // Get all completed trades
        $trades = StockTrade::where('user_id', $userId)
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

        // Filter and enrich with current prices
        $holdings = [];
        $totalPortfolioValue = 0;
        $totalInvested = 0;
        
        foreach ($positions as $symbol => $position) {
            if ($position['quantity'] > 0) {
                $stockPrice = StockPrice::where('symbol', $symbol)->first();
                
                if ($stockPrice) {
                    $avgCost = $position['total_cost'] / $position['quantity'];
                    $currentValue = $position['quantity'] * $stockPrice->price;
                    $profitLoss = $currentValue - $position['total_cost'];
                    $profitLossPercent = $position['total_cost'] > 0 ? ($profitLoss / $position['total_cost']) * 100 : 0;
                    
                    $holdings[] = [
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
                        'percent_change' => $stockPrice->percent_change,
                        'price_change' => $stockPrice->change,
                    ];
                    
                    $totalPortfolioValue += $currentValue;
                    $totalInvested += $position['total_cost'];
                }
            }
        }

        // Calculate concentration percentages
        foreach ($holdings as &$holding) {
            $holding['concentration'] = $totalPortfolioValue > 0 ? ($holding['current_value'] / $totalPortfolioValue) * 100 : 0;
        }

        // Sort by current value descending
        usort($holdings, function($a, $b) {
            return $b['current_value'] <=> $a['current_value'];
        });

        $totalProfitLoss = $totalPortfolioValue - $totalInvested;
        $totalProfitLossPercent = $totalInvested > 0 ? ($totalProfitLoss / $totalInvested) * 100 : 0;

        return view('admin.stock-trading.user-holdings', [
            'user' => $user,
            'holdings' => $holdings,
            'totalPortfolioValue' => $totalPortfolioValue,
            'totalInvested' => $totalInvested,
            'totalProfitLoss' => $totalProfitLoss,
            'totalProfitLossPercent' => $totalProfitLossPercent,
            'holdingsCount' => count($holdings),
            'title' => 'User Holdings - ' . $user->name,
        ]);
    }

    /**
     * Display stock trading settings page
     */
    public function settings()
    {
        $settings = Settings::first();
        
        // Get all unique stock symbols in database
        $allStocks = StockPrice::orderBy('symbol')->get();

        // Get force update cooldown info
        $lastForceUpdate = $settings->last_stock_force_update_at;
        $canForceUpdate = true;
        $cooldownMinutes = 5;
        $nextForceUpdate = null;

        if ($lastForceUpdate) {
            $lastForceUpdate = Carbon::parse($lastForceUpdate);
            $nextForceUpdate = $lastForceUpdate->copy()->addMinutes($cooldownMinutes);
            
            if (Carbon::now()->lt($nextForceUpdate)) {
                $canForceUpdate = false;
            }
        }

        return view('admin.stock-trading.settings', [
            'settings' => $settings,
            'allStocks' => $allStocks,
            'canForceUpdate' => $canForceUpdate,
            'lastForceUpdate' => $lastForceUpdate,
            'nextForceUpdate' => $nextForceUpdate,
            'cooldownMinutes' => $cooldownMinutes,
            'title' => 'Stock Trading Settings',
        ]);
    }

    /**
     * Update stock trading settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enable_stock_trading' => 'required|in:on,off',
            'twelvedata_api_key' => 'nullable|string',
            'stock_market_hours_enabled' => 'required|in:on,off',
            'max_stock_watchlist_items' => 'required|integer|min:1|max:200',
            'stock_trade_fee_percent' => 'required|numeric|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $settings = Settings::first();
        
        $settings->update([
            'enable_stock_trading' => $request->enable_stock_trading,
            'twelvedata_api_key' => $request->twelvedata_api_key,
            'stock_market_hours_enabled' => $request->stock_market_hours_enabled,
            'max_stock_watchlist_items' => $request->max_stock_watchlist_items,
            'stock_trade_fee_percent' => $request->stock_trade_fee_percent,
        ]);

        return back()->with('success', 'Stock trading settings updated successfully');
    }

    /**
     * Force update stock prices (AJAX)
     */
    public function forceUpdate()
    {
        $settings = Settings::first();

        // Check cooldown
        $lastForceUpdate = $settings->last_stock_force_update_at;
        if ($lastForceUpdate) {
            $cooldownMinutes = 5;
            $nextAllowedUpdate = Carbon::parse($lastForceUpdate)->addMinutes($cooldownMinutes);
            
            if (Carbon::now()->lt($nextAllowedUpdate)) {
                $remainingMinutes = abs(Carbon::now()->diffInMinutes($nextAllowedUpdate, false));
                return response()->json([
                    'success' => false,
                    'message' => 'Please wait ' . $remainingMinutes . ' minutes before forcing another update'
                ], 429);
            }
        }

        try {
            // Run the command in background
            Artisan::call('stock:fetch-prices', ['--force' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Stock prices update initiated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update prices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test API connection (AJAX)
     */
    public function testApiConnection(Request $request)
    {
        $apiKey = $request->input('api_key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required'
            ], 400);
        }

        try {
            // Test API with a simple request
            $response = \Illuminate\Support\Facades\Http::get('https://api.twelvedata.com/quote', [
                'symbol' => 'AAPL',
                'apikey' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['code']) && $data['code'] == 401) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid API key'
                    ]);
                }

                if (isset($data['symbol'])) {
                    return response()->json([
                        'success' => true,
                        'message' => 'API connection successful!'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to Twelve Data API'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show manual trade creation form
     */
    public function createTrade($userId = null)
    {
        $users = User::orderBy('name')->get();
        $stocks = StockPrice::orderBy('symbol')->get();
        $settings = Settings::first();
        $feePercent = $settings->stock_trading_fee_percent ?? 1;
        
        $selectedUser = $userId ? User::find($userId) : null;

        return view('admin.stock-trading.create-trade', [
            'users' => $users,
            'stocks' => $stocks,
            'feePercent' => $feePercent,
            'selectedUser' => $selectedUser,
            'title' => 'Create Manual Trade',
        ]);
    }

    /**
     * Store manually created trade
     */
    public function storeTrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'stock_symbol' => 'required|string|max:10',
            'trade_type' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'price' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::findOrFail($request->user_id);
        $settings = Settings::first();
        $feePercent = $settings->stock_trading_fee_percent ?? 1;

        // Calculate amounts
        $totalAmount = $request->quantity * $request->price;
        $feeAmount = StockTrade::calculateFee($totalAmount, $feePercent);
        $netAmount = StockTrade::calculateNetAmount($totalAmount, $feeAmount, $request->trade_type);

        // Create trade
        $trade = StockTrade::create([
            'user_id' => $user->id,
            'stock_symbol' => strtoupper($request->stock_symbol),
            'trade_type' => $request->trade_type,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'total_amount' => $totalAmount,
            'fee_amount' => $feeAmount,
            'fee_percent' => $feePercent,
            'net_amount' => $netAmount,
            'status' => 'completed',
            'notes' => ($request->notes ?? '') . ' [Created manually by admin ' . auth()->guard('admin')->user()->name . ' on ' . now()->format('Y-m-d H:i') . ']',
            'completed_at' => now(),
        ]);

        // Update user balance
        if ($request->trade_type === 'buy') {
            $user->account_bal -= $netAmount;
        } else {
            $user->account_bal += $netAmount;
            // Update ROI for sells
            $profitLoss = $netAmount - ($request->quantity * $request->price);
            $user->roi += $profitLoss;
        }

        // Allow negative balance for admin corrections
        if ($user->account_bal < 0) {
            \Log::warning("Manual trade by admin caused negative balance for user {$user->id}. Balance: {$user->account_bal}");
        }

        $user->save();

        return redirect()->route('admin.stock-trading.user-trades', $user->id)
            ->with('success', 'Manual trade created successfully. User balance updated.');
    }

    /**
     * Cancel a trade (admin override)
     */
    public function cancelTrade($id)
    {
        $trade = StockTrade::findOrFail($id);

        if ($trade->status === 'cancelled') {
            return back()->with('error', 'Trade is already cancelled');
        }

        if ($trade->status === 'completed') {
            return back()->with('error', 'Cannot cancel completed trade. Trade is already processed.');
        }

        $trade->update([
            'status' => 'cancelled',
            'notes' => ($trade->notes ?? '') . ' [Cancelled by admin]',
        ]);

        return back()->with('success', 'Trade cancelled successfully');
    }

    /**
     * Show edit trade form
     */
    public function editTrade($id)
    {
        $trade = StockTrade::with(['user', 'stockPrice'])->findOrFail($id);
        $settings = Settings::first();
        $feePercent = $settings->stock_trading_fee_percent ?? 1;

        return view('admin.stock-trading.edit-trade', [
            'trade' => $trade,
            'feePercent' => $feePercent,
            'title' => 'Edit Trade #' . $trade->id,
        ]);
    }

    /**
     * Update trade with balance reconciliation
     */
    public function updateTrade(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric|min:0.00000001',
            'price' => 'required|numeric|min:0.01',
            'trade_type' => 'required|in:buy,sell',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $trade = StockTrade::with('user')->findOrFail($id);
        $user = $trade->user;

        // Store old values for audit and balance reconciliation
        $oldTradeType = $trade->trade_type;
        $oldQuantity = $trade->quantity;
        $oldPrice = $trade->price;
        $oldNetAmount = $trade->net_amount;

        // Get new values
        $newTradeType = $request->trade_type;
        $newQuantity = $request->quantity;
        $newPrice = $request->price;

        // Calculate new amounts
        $newTotalAmount = $newQuantity * $newPrice;
        $settings = Settings::first();
        $feePercent = $settings->stock_trading_fee_percent ?? 1;
        $newFeeAmount = StockTrade::calculateFee($newTotalAmount, $feePercent);
        $newNetAmount = StockTrade::calculateNetAmount($newTotalAmount, $newFeeAmount, $newTradeType);

        // Balance reconciliation for completed trades
        if ($trade->status === 'completed') {
            // Calculate the balance adjustment needed
            $balanceAdjustment = 0;

            // Reverse old transaction
            if ($oldTradeType === 'buy') {
                $balanceAdjustment += $oldNetAmount; // Refund old buy
            } else {
                $balanceAdjustment -= $oldNetAmount; // Deduct old sell proceeds
            }

            // Apply new transaction
            if ($newTradeType === 'buy') {
                $balanceAdjustment -= $newNetAmount; // Deduct new buy cost
            } else {
                $balanceAdjustment += $newNetAmount; // Credit new sell proceeds
            }

            // Update user balance
            $user->account_bal += $balanceAdjustment;

            // Log negative balance warning but allow
            if ($user->account_bal < 0) {
                \Log::warning("Admin trade edit caused negative balance for user {$user->id}. Balance: {$user->account_bal}");
            }

            $user->save();
        }

        // Build audit note
        $changes = [];
        if ($oldTradeType !== $newTradeType) {
            $changes[] = "type: {$oldTradeType}→{$newTradeType}";
        }
        if ($oldQuantity != $newQuantity) {
            $changes[] = "qty: {$oldQuantity}→{$newQuantity}";
        }
        if ($oldPrice != $newPrice) {
            $changes[] = "price: \${$oldPrice}→\${$newPrice}";
        }

        $auditNote = " [Edited by admin " . auth()->guard('admin')->user()->name . " on " . now()->format('Y-m-d H:i') . ": " . implode(', ', $changes) . "]";

        // Update trade
        $trade->update([
            'trade_type' => $newTradeType,
            'quantity' => $newQuantity,
            'price' => $newPrice,
            'total_amount' => $newTotalAmount,
            'fee_amount' => $newFeeAmount,
            'fee_percent' => $feePercent,
            'net_amount' => $newNetAmount,
            'notes' => ($trade->notes ?? '') . $auditNote,
        ]);

        return redirect()->route('admin.stock-trading.user-trades', $user->id)
            ->with('success', 'Trade updated successfully. Balance adjusted.');
    }

    /**
     * Delete a trade record
     */
    public function deleteTrade($id)
    {
        $trade = StockTrade::findOrFail($id);

        if ($trade->status === 'completed') {
            return back()->with('error', 'Cannot delete completed trade. Please cancel it first if needed.');
        }

        $trade->delete();

        return back()->with('success', 'Trade record deleted successfully');
    }

    /**
     * Bulk import stocks from CSV
     */
    public function bulkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        try {
            $file = $request->file('stock_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));

            $symbols = [];
            foreach ($data as $row) {
                if (!empty($row[0])) {
                    $symbols[] = strtoupper(trim($row[0]));
                }
            }

            if (empty($symbols)) {
                return back()->with('error', 'No valid stock symbols found in CSV file');
            }

            // Run initial fetch for all symbols
            Artisan::call('stock:fetch-prices', [
                '--symbols' => implode(',', $symbols),
                '--force' => true
            ]);

            return back()->with('success', 'Bulk import initiated for ' . count($symbols) . ' stocks');

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Add preset stocks (S&P 500, NASDAQ 100, DOW 30)
     */
    public function addPreset(Request $request)
    {
        $preset = $request->input('preset');

        $presets = [
            'sp500' => $this->getSP500Symbols(),
            'nasdaq100' => $this->getNASDAQ100Symbols(),
            'dow30' => $this->getDOW30Symbols(),
        ];

        if (!isset($presets[$preset])) {
            return back()->with('error', 'Invalid preset selected');
        }

        $symbols = $presets[$preset];

        try {
            // Run initial fetch for preset symbols (in batches to avoid timeout)
            Artisan::call('stock:fetch-prices', [
                '--symbols' => implode(',', array_slice($symbols, 0, 50)), // First 50
                '--force' => true
            ]);

            return back()->with('success', 'Started importing ' . count($symbols) . ' stocks from ' . strtoupper($preset) . '. This may take several minutes.');

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get popular DOW 30 symbols
     */
    private function getDOW30Symbols()
    {
        return ['AAPL', 'MSFT', 'JPM', 'V', 'UNH', 'JNJ', 'WMT', 'PG', 'HD', 'CVX', 
                'MRK', 'DIS', 'VZ', 'CSCO', 'KO', 'NKE', 'INTC', 'AXP', 'MCD', 'IBM',
                'CAT', 'GS', 'BA', 'MMM', 'TRV', 'DOW', 'HON', 'WBA', 'AMGN', 'CRM'];
    }

    /**
     * Get popular NASDAQ 100 symbols (subset)
     */
    private function getNASDAQ100Symbols()
    {
        return ['AAPL', 'MSFT', 'GOOGL', 'GOOG', 'AMZN', 'NVDA', 'META', 'TSLA', 'AVGO', 'COST',
                'PEP', 'ADBE', 'NFLX', 'CSCO', 'CMCSA', 'INTC', 'AMD', 'INTU', 'QCOM', 'TXN',
                'AMAT', 'HON', 'SBUX', 'ISRG', 'ADI', 'BKNG', 'GILD', 'REGN', 'VRTX', 'ADP'];
    }

    /**
     * Get popular S&P 500 symbols (subset)
     */
    private function getSP500Symbols()
    {
        return ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'NVDA', 'META', 'BRK.B', 'TSLA', 'UNH', 'JNJ',
                'V', 'WMT', 'XOM', 'JPM', 'PG', 'MA', 'LLY', 'AVGO', 'HD', 'CVX',
                'MRK', 'KO', 'ABBV', 'PEP', 'COST', 'ADBE', 'MCD', 'TMO', 'CSCO', 'ABT'];
    }

    /**
     * Manage stock prices (CRUD interface)
     */
    public function manageStocks()
    {
        $stocks = StockPrice::orderBy('symbol')->paginate(50);
        
        return view('admin.stock-trading.manage-stocks', [
            'stocks' => $stocks,
            'title' => 'Manage Stock Prices',
        ]);
    }

    /**
     * Show edit stock form
     */
    public function editStock($id)
    {
        $stock = StockPrice::findOrFail($id);
        
        return view('admin.stock-trading.edit-stock', [
            'stock' => $stock,
            'title' => 'Edit Stock - ' . $stock->symbol,
        ]);
    }

    /**
     * Update stock
     */
    public function updateStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string|max:10|unique:stock_prices,symbol,' . $id,
            'name' => 'required|string|max:255',
            'exchange' => 'nullable|string|max:50',
            'logo_url' => 'nullable|url|max:500',
            'price' => 'required|numeric|min:0',
            'open' => 'nullable|numeric|min:0',
            'high' => 'nullable|numeric|min:0',
            'low' => 'nullable|numeric|min:0',
            'previous_close' => 'nullable|numeric|min:0',
            'volume' => 'nullable|integer|min:0',
            'market_cap' => 'nullable|numeric|min:0',
            'pe_ratio' => 'nullable|numeric',
            'dividend_yield' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $stock = StockPrice::findOrFail($id);

        // Calculate change and percent_change
        $price = $request->price;
        $previousClose = $request->previous_close ?? $stock->previous_close ?? $price;
        $change = $price - $previousClose;
        $percentChange = $previousClose > 0 ? (($change / $previousClose) * 100) : 0;

        $stock->update([
            'symbol' => strtoupper($request->symbol),
            'name' => $request->name,
            'exchange' => $request->exchange,
            'logo_url' => $request->logo_url,
            'price' => $price,
            'open' => $request->open ?? $price,
            'high' => $request->high ?? $price,
            'low' => $request->low ?? $price,
            'previous_close' => $previousClose,
            'change' => $change,
            'percent_change' => $percentChange,
            'volume' => $request->volume ?? 0,
            'market_cap' => $request->market_cap,
            'pe_ratio' => $request->pe_ratio,
            'dividend_yield' => $request->dividend_yield,
            'last_updated' => now(),
        ]);

        return redirect()->route('admin.stock-trading.manage-stocks')
            ->with('success', 'Stock updated successfully');
    }

    /**
     * Show create stock form
     */
    public function createStock()
    {
        return view('admin.stock-trading.create-stock', [
            'title' => 'Add New Stock',
        ]);
    }

    /**
     * Store new stock
     */
    public function storeStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string|max:10|unique:stock_prices,symbol',
            'name' => 'required|string|max:255',
            'exchange' => 'nullable|string|max:50',
            'logo_url' => 'nullable|url|max:500',
            'price' => 'required|numeric|min:0',
            'open' => 'nullable|numeric|min:0',
            'high' => 'nullable|numeric|min:0',
            'low' => 'nullable|numeric|min:0',
            'previous_close' => 'nullable|numeric|min:0',
            'volume' => 'nullable|integer|min:0',
            'market_cap' => 'nullable|numeric|min:0',
            'pe_ratio' => 'nullable|numeric',
            'dividend_yield' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Calculate change and percent_change
        $price = $request->price;
        $previousClose = $request->previous_close ?? $price;
        $change = $price - $previousClose;
        $percentChange = $previousClose > 0 ? (($change / $previousClose) * 100) : 0;

        StockPrice::create([
            'symbol' => strtoupper($request->symbol),
            'name' => $request->name,
            'exchange' => $request->exchange ?? 'NASDAQ',
            'logo_url' => $request->logo_url,
            'price' => $price,
            'open' => $request->open ?? $price,
            'high' => $request->high ?? $price,
            'low' => $request->low ?? $price,
            'previous_close' => $previousClose,
            'change' => $change,
            'percent_change' => $percentChange,
            'volume' => $request->volume ?? 0,
            'market_cap' => $request->market_cap,
            'pe_ratio' => $request->pe_ratio,
            'dividend_yield' => $request->dividend_yield,
            'last_updated' => now(),
        ]);

        return redirect()->route('admin.stock-trading.manage-stocks')
            ->with('success', 'Stock added successfully');
    }

    /**
     * Delete stock
     */
    public function deleteStock($id)
    {
        $stock = StockPrice::findOrFail($id);
        
        // Check if stock has trades
        $tradesCount = $stock->trades()->count();
        
        if ($tradesCount > 0) {
            return back()->with('error', 'Cannot delete stock with existing trades. Found ' . $tradesCount . ' trade(s).');
        }

        $stock->delete();

        return back()->with('success', 'Stock deleted successfully');
    }
}
