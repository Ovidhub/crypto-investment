<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TradingWatchlist;
use App\Models\TradingWatchlistItem;
use App\Models\TradingPreference;
use App\Models\TradingOrder;
use App\Services\TradingApiService;
use Illuminate\Support\Facades\Log;
use App\Models\Tp_Transaction;

class TradingController extends Controller
{
    /**
     * Trading API Service
     */
    protected $tradingApiService;
    
    /**
     * Create a new controller instance.
     */
    public function __construct(TradingApiService $tradingApiService)
    {
        $this->tradingApiService = $tradingApiService;
    }
    
    /**
     * Display the main trading dashboard.
     */
    public function index()
    {
        // Get user's default crypto watchlist or create one
        $cryptoWatchlist = TradingWatchlist::where('user_id', Auth::user()->id)
            ->where('type', 'crypto')
            ->where('is_default', true)
            ->first();
            
        if (!$cryptoWatchlist) {
            $cryptoWatchlist = TradingWatchlist::createDefaultForUser(Auth::user()->id, 'crypto');
            
            // Add some default crypto pairs
            $defaultPairs = [
                ['symbol' => 'BTCUSDT', 'base_currency' => 'BTC', 'quote_currency' => 'USDT'],
                ['symbol' => 'ETHUSDT', 'base_currency' => 'ETH', 'quote_currency' => 'USDT'],
                ['symbol' => 'BNBUSDT', 'base_currency' => 'BNB', 'quote_currency' => 'USDT'],
                ['symbol' => 'SOLUSDT', 'base_currency' => 'SOL', 'quote_currency' => 'USDT'],
            ];
            
            foreach ($defaultPairs as $pair) {
                TradingWatchlistItem::addToDefaultWatchlist(
                    Auth::user()->id,
                    $pair['symbol'],
                    'crypto',
                    [
                        'base_currency' => $pair['base_currency'],
                        'quote_currency' => $pair['quote_currency']
                    ]
                );
            }
        }
        
        // Get user's default stock watchlist or create one
        $stockWatchlist = TradingWatchlist::where('user_id', Auth::user()->id)
            ->where('type', 'stock')
            ->where('is_default', true)
            ->first();
            
        if (!$stockWatchlist) {
            $stockWatchlist = TradingWatchlist::createDefaultForUser(Auth::user()->id, 'stock');
            
            // Add some default stocks
            $defaultStocks = [
                ['symbol' => 'AAPL', 'market' => 'NASDAQ'],
                ['symbol' => 'MSFT', 'market' => 'NASDAQ'],
                ['symbol' => 'GOOG', 'market' => 'NASDAQ'],
                ['symbol' => 'AMZN', 'market' => 'NASDAQ'],
            ];
            
            foreach ($defaultStocks as $stock) {
                TradingWatchlistItem::addToDefaultWatchlist(
                    Auth::user()->id,
                    $stock['symbol'],
                    'stock',
                    [
                        'market' => $stock['market']
                    ]
                );
            }
        }
        
        // Get top cryptos for market overview
        $topCryptos = $this->tradingApiService->getTopCryptos(10);
        
        // Get user preferences
        $preferences = TradingPreference::getAllPreferences(Auth::user()->id);
        
        return view('user.trading.index', [
            'cryptoWatchlist' => $cryptoWatchlist,
            'stockWatchlist' => $stockWatchlist,
            'topCryptos' => $topCryptos,
            'preferences' => $preferences,
        ]);
    }
    
    /**
     * Display the crypto trading page.
     */
    public function crypto(Request $request)
    {
        // Get the symbol from the request (default to BTCUSDT)
        $symbol = $request->input('symbol', 'BTCUSDT');

        // Get crypto ticker info
        try {
            $tradingApiService = app(TradingApiService::class);
            $ticker = $tradingApiService->getHuobiTicker($symbol);
            $isFallback = isset($ticker['is_fallback']) && $ticker['is_fallback'];
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching crypto ticker: ' . $e->getMessage());
            
            // Use fallback data
            $ticker = [
                'symbol' => $symbol,
                'lastPrice' => $symbol == 'BTCUSDT' ? 30000 : ($symbol == 'ETHUSDT' ? 2000 : 1),
                'priceChange' => 0,
                'priceChangePercent' => 0,
                'highPrice' => $symbol == 'BTCUSDT' ? 30100 : ($symbol == 'ETHUSDT' ? 2010 : 1.1),
                'lowPrice' => $symbol == 'BTCUSDT' ? 29900 : ($symbol == 'ETHUSDT' ? 1990 : 0.9),
                'volume' => 0,
                'quoteVolume' => 0,
                'source' => 'dummy_data'
            ];
            $isFallback = true;
        }
        
        // Format ticker data for view
        $tickerFormatted = [
            'symbol' => $ticker['symbol'],
            'price' => number_format((float)$ticker['lastPrice'], 2),
            'change' => number_format((float)$ticker['priceChange'], 2),
            'changePercent' => number_format((float)$ticker['priceChangePercent'], 2),
            'volume' => number_format((float)$ticker['volume']),
            'high' => number_format((float)$ticker['highPrice'], 2),
            'low' => number_format((float)$ticker['lowPrice'], 2),
            'source' => $ticker['source'] ?? 'huobi'
        ];
        
        // Check if symbol is in user's watchlist
        $inWatchlist = TradingWatchlistItem::whereHas('watchlist', function($query) {
            $query->where('user_id', Auth::user()->id);
        })->where('symbol', $symbol)->exists();
        
        // Get user's default crypto watchlist
        $watchlist = TradingWatchlist::where('user_id', Auth::user()->id)
            ->where('type', 'crypto')
            ->where('is_default', true)
            ->with('items')
            ->first();
        
        if (!$watchlist) {
            $watchlist = TradingWatchlist::createDefaultForUser(Auth::user()->id, 'crypto');
        }
        
        // Get open orders for this symbol
        $openOrders = TradingOrder::where('user_id', Auth::user()->id)
            ->where('symbol', $symbol)
            ->where('market_type', 'crypto')
            ->whereIn('status', ['open', 'pending', 'partially_filled', 'filled'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('user.trading.crypto', [
            'symbol' => $symbol,
            'ticker' => $tickerFormatted,
            'isFallback' => $isFallback,
            'openOrders' => $openOrders,
            'timeframe' => '1h', // Default timeframe for chart
            'inWatchlist' => $inWatchlist, // Pass watchlist status to view
            'watchlist' => $watchlist // Pass the watchlist to the view
        ]);
    }
    
    /**
     * Show stock trading view.
     */
    public function stocks(Request $request, $symbol = 'AAPL')
    {
        // If symbol is passed via query parameter, prioritize it over route parameter
        if ($request->has('symbol') && !empty($request->symbol)) {
            $symbol = $request->symbol;
        }
        
        try {
            // Check if symbol is in user's watchlist
            $inWatchlist = TradingWatchlistItem::whereHas('watchlist', function($query) {
                $query->where('user_id', Auth::user()->id)->where('type', 'stock');
            })->where('symbol', $symbol)->first();
            
            // Get user's default stock watchlist
            $watchlist = TradingWatchlist::where('user_id', Auth::user()->id)
                ->where('type', 'stock')
                ->where('is_default', true)
                ->first();
                
            // Create default watchlist if not exists
            if (!$watchlist) {
                $watchlist = TradingWatchlist::createDefaultForUser(Auth::user()->id, 'stock');
            }

            // Get the trading API service
            $tradingApiService = app(TradingApiService::class);
            
            // Get stock quote from Finnhub API
            $stockQuote = $tradingApiService->getStockQuoteFinnhub($symbol);
            $isFallback = ($stockQuote['source'] ?? '') !== 'finnhub';
            
            // Get company profile (general info) from Finnhub
            $companyProfile = $tradingApiService->getCompanyProfileFinnhub($symbol);
            
            // Get company metrics (financial data) from Finnhub
            $companyMetrics = $tradingApiService->getCompanyMetricsFinnhub($symbol);
            
            // Extract company information into separate arrays for view
            $companyInfo = [
                'name' => $companyProfile['name'] ?? $symbol,
                'sector' => $companyProfile['sector'] ?? 'N/A',
                'industry' => $companyProfile['industry'] ?? 'N/A',
                'employees' => $companyProfile['employees'] ? number_format($companyProfile['employees']) : 'N/A',
                'country' => $companyProfile['country'] ?? 'N/A',
                'exchange' => $companyProfile['exchange'] ?? 'NASDAQ',
                'description' => $companyProfile['description'] ?? "No description available for {$symbol}."
            ];
            
            $companyStats = [
                'marketCap' => $companyProfile['marketCapFormatted'] ?? $companyMetrics['marketCapFormatted'] ?? 'N/A',
                'peRatio' => $companyMetrics['peRatioFormatted'] ?? 'N/A',
                'eps' => $companyMetrics['epsFormatted'] ?? 'N/A',
                'dividendYield' => $companyMetrics['dividendYieldFormatted'] ?? 'N/A',
                '52WeekRange' => $companyMetrics['52WeekRange'] ?? 'N/A',
                'beta' => isset($companyMetrics['beta']) ? number_format($companyMetrics['beta'], 2) : 'N/A'
            ];
            
            // Format price data for display
            $ticker = [
                'symbol' => $stockQuote['symbol'] ?? $symbol,
                'lastPrice' => number_format((float)($stockQuote['price'] ?? 0), 2),
                'change' => number_format((float)($stockQuote['change'] ?? 0), 2),
                'changePercent' => number_format((float)($stockQuote['changePercent'] ?? 0), 2) . '%',
                'open' => number_format((float)($stockQuote['open'] ?? 0), 2),
                'high' => number_format((float)($stockQuote['high'] ?? 0), 2),
                'low' => number_format((float)($stockQuote['low'] ?? 0), 2),
                'prevClose' => number_format((float)($stockQuote['prevClose'] ?? 0), 2),
                'volume' => number_format((float)($stockQuote['volume'] ?? 0)),
                'marketStatus' => $this->getMarketStatus()['status'] ?? 'closed'
            ];
            
            $data = [
                'symbol' => $symbol,
                'fullName' => $companyInfo['name'],
                'quote' => $stockQuote,
                'ticker' => $ticker,
                'companyInfo' => $companyInfo,
                'companyStats' => $companyStats,
                'isFallback' => $isFallback,
                'apiProvider' => 'Finnhub',
                'marketStatus' => $this->getMarketStatus(),
                'inWatchlist' => (bool)$inWatchlist,
                'watchlistItemId' => $inWatchlist ? $inWatchlist->id : null,
                'watchlist' => $watchlist
            ];
            
            if (isset($stockQuote['is_fallback']) && $stockQuote['is_fallback']) {
                $data['isFallback'] = true;
                $data['apiProvider'] = isset($stockQuote['source']) ? ucfirst($stockQuote['source']) : 'Fallback Data';
            }
            
            return view('user.trading.stocks', $data);
        } catch (\Exception $e) {
            Log::error('Error loading stock page: ' . $e->getMessage(), ['symbol' => $symbol]);
            
            // Fallback to sample data
            $stockQuote = $this->getSampleStockData($symbol);
            
            return view('user.trading.stocks', [
                'symbol' => $symbol,
                'fullName' => $symbol . ' Inc.',
                'quote' => $stockQuote,
                'ticker' => [
                    'symbol' => $symbol,
                    'lastPrice' => number_format($stockQuote['price'], 2),
                    'change' => number_format($stockQuote['change'], 2),
                    'changePercent' => number_format($stockQuote['changePercent'], 2) . '%',
                    'open' => number_format($stockQuote['open'], 2),
                    'high' => number_format($stockQuote['high'], 2),
                    'low' => number_format($stockQuote['low'], 2),
                    'prevClose' => number_format($stockQuote['prevClose'], 2),
                    'volume' => number_format($stockQuote['volume']),
                    'marketStatus' => 'closed'
                ],
                'companyInfo' => [
                    'name' => $symbol . ' Inc.',
                    'sector' => 'Technology',
                    'industry' => 'Software',
                    'employees' => number_format(rand(1000, 50000)),
                    'country' => 'United States',
                    'exchange' => 'NASDAQ',
                    'description' => "Sample description for {$symbol} Inc., a leading company in its industry."
                ],
                'companyStats' => [
                    'marketCap' => '$' . number_format(rand(1, 100), 2) . 'B',
                    'peRatio' => number_format(rand(10, 40), 2),
                    'eps' => '$' . number_format(rand(1, 10), 2),
                    'dividendYield' => number_format(rand(0, 3), 2) . '%',
                    '52WeekRange' => '$' . number_format(rand(50, 90), 2) . ' - $' . number_format(rand(100, 200), 2),
                    'beta' => number_format(rand(80, 120) / 100, 2)
                ],
                'isFallback' => true,
                'apiProvider' => 'Sample Data',
                'marketStatus' => ['status' => 'closed', 'nextOpen' => '9:30 AM ET'],
                'inWatchlist' => false,
                'watchlistItemId' => null
            ]);
        }
    }
    
    /**
     * Show specific crypto symbol details.
     */
    public function showCrypto($symbol)
    {
        return $this->crypto(new Request(['symbol' => $symbol]));
    }
    
    /**
     * Show specific stock symbol details with proper redirection.
     */
    public function showStocks($symbol)
    {
        // Validate symbol
        if (!$symbol || strlen($symbol) < 1 || strlen($symbol) > 10) {
            return redirect()->route('trading.stocks');
        }
        
        // Create request with symbol parameter
        $request = new Request(['symbol' => strtoupper($symbol)]);
        
        return $this->stocks($request, strtoupper($symbol));
    }
    
    /**
     * Get user's watchlists.
     */
    public function getWatchlist(Request $request)
    {
        $type = $request->query('type', 'crypto');
        
        $watchlists = TradingWatchlist::where('user_id', Auth::user()->id)
            ->where('type', $type)
            ->with('items')
            ->orderBy('is_default', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();
        
        return response()->json($watchlists);
    }
    
    /**
     * Add a symbol to user's watchlist.
     */
    public function addToWatchlist(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
            'type' => 'required|in:crypto,stock',
            'watchlist_id' => 'nullable|integer',
        ]);
        
        $symbol = $request->input('symbol');
        $type = $request->input('type');
        $watchlistId = $request->input('watchlist_id');
        
        try {
            // First check if the symbol already exists in any of user's watchlists
            $existingItem = TradingWatchlistItem::whereHas('watchlist', function($query) {
                $query->where('user_id', Auth::user()->id);
            })->where('symbol', $symbol)->first();

            if ($existingItem) {
                return response()->json([
                    'success' => true,
                    'message' => 'Symbol already in watchlist',
                    'item' => $existingItem,
                    'item_id' => $existingItem->id
                ]);
            }

            // Parse base and quote currencies for crypto
            $data = [];
            if ($type === 'crypto') {
                // Handle USDT pairs
                if (str_ends_with($symbol, 'USDT')) {
                    $base = substr($symbol, 0, -4);
                    $quote = 'USDT';
                }
                // Handle other pairs (assuming USD or other 3-letter currency)
                else {
                    $base = substr($symbol, 0, -3);
                    $quote = substr($symbol, -3);
                }
                
                $data = [
                    'base_currency' => $base,
                    'quote_currency' => $quote
                ];
            }

            // Add to default watchlist
            $item = TradingWatchlistItem::addToDefaultWatchlist(Auth::user()->id, $symbol, $type, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Symbol added to watchlist',
                'item' => $item,
                'item_id' => $item->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error adding symbol to watchlist: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error adding symbol to watchlist: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove a symbol from user's watchlist.
     */
    public function removeFromWatchlist($id)
    {
        try {
            $item = TradingWatchlistItem::whereHas('watchlist', function($query) {
                $query->where('user_id', Auth::user()->id);
            })->findOrFail($id);
            
            $symbol = $item->symbol;
            $item->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Symbol removed from watchlist',
                'symbol' => $symbol
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing symbol from watchlist: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error removing symbol from watchlist: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get user preferences.
     */
    public function getPreferences(Request $request)
    {
        $category = $request->query('category');
        
        try {
            $preferences = TradingPreference::getAllPreferences(Auth::user()->id, $category);
            
            return response()->json($preferences);
        } catch (\Exception $e) {
            Log::error('Error getting preferences: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error getting preferences: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Save user preferences.
     */
    public function savePreferences(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'category' => 'nullable|string',
        ]);
        
        $key = $request->input('key');
        $value = $request->input('value');
        $category = $request->input('category', 'general');
        
        try {
            $preference = TradingPreference::setPreference(Auth::user()->id, $key, $value, $category);
            
            return response()->json([
                'message' => 'Preference saved successfully',
                'preference' => $preference
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving preference: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error saving preference: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get crypto market data.
     */
    public function getCryptoMarket()
    {
        try {
            $topCryptos = $this->tradingApiService->getTopCryptos(50);
            
            return response()->json($topCryptos);
        } catch (\Exception $e) {
            Log::error('Error getting crypto market data: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error getting crypto market data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get stock market data.
     */
    public function getStockMarket()
    {
        try {
            $tradingApiService = app(TradingApiService::class);
            $popularStocks = ['AAPL', 'MSFT', 'GOOG', 'AMZN', 'TSLA', 'META', 'NVDA', 'JPM', 'V', 'WMT'];
            $stockData = [];
            
            foreach ($popularStocks as $symbol) {
                $quote = $tradingApiService->getStockQuoteFinnhub($symbol);
                if ($quote) {
                    $stockData[] = $quote;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => $stockData
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting stock market data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting stock market data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get crypto symbol ticker data (AJAX)
     */
    public function getCryptoSymbol(Request $request)
    {
        $symbol = $request->input('symbol', 'BTCUSDT');
        
        try {
            $tradingApiService = app(TradingApiService::class);
            $ticker = $tradingApiService->getHuobiTicker($symbol);
            
            // Return JSON response
            return response()->json([
                'symbol' => $ticker['symbol'],
                'lastPrice' => (float)$ticker['lastPrice'],
                'priceChange' => (float)$ticker['priceChange'],
                'priceChangePercent' => (float)$ticker['priceChangePercent'],
                'volume' => (float)$ticker['volume'],
                'quoteVolume' => (float)($ticker['quoteVolume'] ?? 0),
                'highPrice' => (float)$ticker['highPrice'],
                'lowPrice' => (float)$ticker['lowPrice'],
                'openPrice' => (float)($ticker['openPrice'] ?? $ticker['lastPrice']),
                'isFallback' => isset($ticker['is_fallback']) && $ticker['is_fallback'],
                'source' => $ticker['source'] ?? 'huobi'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching crypto ticker for AJAX: ' . $e->getMessage());
            
            // Create a dummy price based on symbol
            $dummyPrice = $symbol == 'BTCUSDT' ? 30000 : ($symbol == 'ETHUSDT' ? 2000 : 1);
            
            // Return fallback data
            return response()->json([
                'symbol' => $symbol,
                'lastPrice' => $dummyPrice,
                'priceChange' => 0,
                'priceChangePercent' => 0,
                'volume' => 0,
                'quoteVolume' => 0,
                'highPrice' => $dummyPrice * 1.01,
                'lowPrice' => $dummyPrice * 0.99,
                'openPrice' => $dummyPrice,
                'isFallback' => true,
                'source' => 'dummy_data'
            ]);
        }
    }
    
    /**
     * Get stock quote data from Finnhub API
     */
    public function getStockQuoteFinnhub($symbol)
    {
        $tradingApiService = app(TradingApiService::class);
        
        try {
            $quote = $tradingApiService->getStockQuoteFinnhub($symbol);
            
            if (!empty($quote)) {
                // Format data for response, ensuring volume is included
                $data = [
                    'success' => true,
                    'symbol' => $quote['symbol'],
                    'price' => floatval($quote['price']),
                    'priceFormatted' => '$' . number_format(floatval($quote['price']), 2),
                    'change' => floatval($quote['change']),
                    'changePercent' => floatval($quote['changePercent']),
                    'high' => floatval($quote['high'] ?? 0),
                    'low' => floatval($quote['low'] ?? 0),
                    'open' => floatval($quote['open'] ?? 0),
                    'prevClose' => floatval($quote['prevClose'] ?? 0),
                    'volume' => intval($quote['volume'] ?? 0), // Ensure volume is an integer
                    'volumeFormatted' => number_format(intval($quote['volume'] ?? 0)), // Format for display
                    'name' => $quote['name'] ?? $symbol,
                    'is_fallback' => isset($quote['is_fallback']) ? $quote['is_fallback'] : false,
                    'source' => $quote['source'] ?? 'finnhub'
                ];
                
                return response()->json($data);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock quote not found'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving stock quote: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving stock quote'
            ]);
        }
    }
    
    /**
     * Get stock symbol details.
     */
    public function getStockSymbol($symbol, Request $request)
    {
        try {
            $tradingApiService = app(TradingApiService::class);
            $quote = $tradingApiService->getStockQuoteFinnhub($symbol);
            
            if ($quote) {
                return response()->json($quote);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Symbol not found'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error getting stock symbol: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting stock symbol: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a new order.
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'symbol' => 'required|string',
            'order_type' => 'required|in:market,limit,stop_limit',
            'side' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'price' => 'required_if:order_type,limit,stop_limit|nullable|numeric|min:0',
            'stop_price' => 'required_if:order_type,stop_limit|nullable|numeric|min:0',
            'market_type' => 'required|in:crypto,stock',
        ]);
        
        $symbol = $request->input('symbol');
        $type = $request->input('order_type');
        $side = $request->input('side');
        $quantity = $request->input('quantity');
        $price = $request->input('price');
        $stopPrice = $request->input('stop_price');
        $marketType = $request->input('market_type');
        
        try {
            // Verify symbol exists and get current price if needed
            if ($marketType === 'crypto') {
                $ticker = $this->tradingApiService->getHuobiTicker($symbol);
                
                if (!$ticker) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid symbol'
                    ], 400);
                }
                
                if ($type === 'market') {
                    $price = $ticker['lastPrice'];
                }
            } else {
                $tradingApiService = app(TradingApiService::class);
                $quote = $tradingApiService->getStockQuoteFinnhub($symbol);
                
                if (!$quote) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid symbol'
                    ], 400);
                }
                
                if ($type === 'market') {
                    $price = $quote['price'];
                }
            }
            
            // Calculate total value of order
            $total = $quantity * $price;
            
            // Get user's balance to verify they have enough funds
            if ($side === 'buy') {
                $user = Auth::user();
                $balance = $user->account_bal;
                
                if ($total > $balance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient funds'
                    ], 400);
                }
                
                // For market orders, deduct immediately
                if ($type === 'market') {
                    // Deduct from user's balance for buy orders
                    $user->account_bal = $balance - $total;
                    $user->save();
                    
                    // Create transaction record
                    Tp_Transaction::create([
                        'user' => Auth::user()->id,
                        'plan' => "Crypto Trading",
                        'amount' => $total,
                        'type' => "Trading Debit",
                        'remark' => "Purchase of {$quantity} {$symbol}",
                    ]);
                }
            } else {
                // For sell orders, check if user has enough assets
                // @TODO: Implement proper asset balance check for selling crypto
                // For now, we'll assume the user has enough of the asset
                
                // For market orders, credit immediately
                if ($type === 'market') {
                    $user = Auth::user();
                    $previousBalance = $user->account_bal;
                    $user->account_bal = $previousBalance + $total;
                    $user->save();
                    
                    // Create transaction record
                    Tp_Transaction::create([
                        'user' => Auth::user()->id,
                        'plan' => "Crypto Trading",
                        'amount' => $total,
                        'type' => "Trading Credit",
                        'remark' => "Sale of {$quantity} {$symbol}",
                    ]);
                }
            }
            
            // Create new order
            $order = new TradingOrder();
            $order->user_id = Auth::user()->id;
            $order->symbol = $symbol;
            $order->order_type = $type;
            $order->side = $side;
            $order->quantity = $quantity;
            $order->price = $price;
            $order->stop_price = $stopPrice;
            $order->total_cost = $total;
            $order->status = $type === 'market' ? 'filled' : 'open';
            $order->market_type = $marketType;
            $order->filled_at = $type === 'market' ? now() : null;
            
            $order->save();
            
            // Return response
            return response()->json([
                'success' => true,
                'message' => $type === 'market' ? 'Order filled successfully' : 'Order placed successfully',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get user's orders.
     */
    public function getOrders(Request $request)
    {
        $symbol = $request->query('symbol');
        $marketType = $request->query('market_type');
        $status = $request->query('status');
        $limit = $request->query('limit', 50);
        
        try {
            $query = TradingOrder::where('user_id', Auth::user()->id);
            
            if ($symbol) {
                $query->where('symbol', $symbol);
            }
            
            if ($marketType) {
                $query->where('market_type', $marketType);
            }
            
            if ($status) {
                if ($status === 'open') {
                    $query->whereIn('status', ['pending', 'partially_filled', 'open']);
                } else if ($status === 'completed') {
                    $query->whereIn('status', ['filled', 'cancelled']);
                } else {
                    $query->where('status', $status);
                }
            }
            
            $orders = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting orders: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting orders: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get a specific order.
     */
    public function getOrder($id)
    {
        try {
            $order = TradingOrder::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->first();
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or you do not have permission to view it'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving order details: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancel an order.
     */
    public function cancelOrder($id)
    {
        try {
            $order = TradingOrder::where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->first();
                
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or you do not have permission to cancel it'
                ], 404);
            }
            
            if (!$order->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be cancelled'
                ], 400);
            }
            
            $success = $order->cancel();
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully',
                    'data' => $order
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error cancelling order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get top cryptocurrencies
     */
    public function getTopCryptos()
    {
        try {
            $tradingApiService = app(TradingApiService::class);
            $cryptos = $tradingApiService->getTopCryptos();
            
            // If empty result, throw exception to trigger fallback
            if (empty($cryptos)) {
                throw new \Exception("Empty data returned from API");
            }
            
            return response()->json([
                'success' => true,
                'data' => $cryptos
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching top cryptocurrencies: ' . $e->getMessage());
            
            // Provide reliable fallback data
            $fallbackData = [
                [
                    'symbol' => 'BTCUSDT',
                    'name' => 'Bitcoin',
                    'price' => 39750.25,
                    'priceFormatted' => '$39,750.25',
                    'change24h' => 2.35,
                    'change24hFormatted' => '+2.35%',
                    'marketCap' => 775000000000,
                    'marketCapFormatted' => '$775B',
                    'volume24h' => 24500000000,
                    'volume24hFormatted' => '$24.5B',
                    'lastPrice' => 39750.25,
                    'priceChangePercent' => 2.35,
                    'baseAsset' => 'BTC',
                    'quoteAsset' => 'USDT'
                ],
                [
                    'symbol' => 'ETHUSDT',
                    'name' => 'Ethereum',
                    'price' => 2187.45,
                    'priceFormatted' => '$2,187.45',
                    'change24h' => 1.78,
                    'change24hFormatted' => '+1.78%',
                    'marketCap' => 262900000000,
                    'marketCapFormatted' => '$262.9B',
                    'volume24h' => 12100000000,
                    'volume24hFormatted' => '$12.1B',
                    'lastPrice' => 2187.45,
                    'priceChangePercent' => 1.78,
                    'baseAsset' => 'ETH',
                    'quoteAsset' => 'USDT'
                ],
                [
                    'symbol' => 'BNBUSDT',
                    'name' => 'Binance Coin',
                    'price' => 315.65,
                    'priceFormatted' => '$315.65',
                    'change24h' => -0.45,
                    'change24hFormatted' => '-0.45%',
                    'marketCap' => 48300000000,
                    'marketCapFormatted' => '$48.3B',
                    'volume24h' => 850000000,
                    'volume24hFormatted' => '$850M',
                    'lastPrice' => 315.65,
                    'priceChangePercent' => -0.45,
                    'baseAsset' => 'BNB',
                    'quoteAsset' => 'USDT'
                ],
                [
                    'symbol' => 'SOLUSDT',
                    'name' => 'Solana',
                    'price' => 109.80,
                    'priceFormatted' => '$109.80',
                    'change24h' => 3.25,
                    'change24hFormatted' => '+3.25%',
                    'marketCap' => 47700000000,
                    'marketCapFormatted' => '$47.7B',
                    'volume24h' => 1820000000,
                    'volume24hFormatted' => '$1.82B',
                    'lastPrice' => 109.80,
                    'priceChangePercent' => 3.25,
                    'baseAsset' => 'SOL',
                    'quoteAsset' => 'USDT'
                ],
                [
                    'symbol' => 'ADAUSDT',
                    'name' => 'Cardano',
                    'price' => 0.473,
                    'priceFormatted' => '$0.473',
                    'change24h' => -1.20,
                    'change24hFormatted' => '-1.20%',
                    'marketCap' => 16700000000,
                    'marketCapFormatted' => '$16.7B',
                    'volume24h' => 365000000,
                    'volume24hFormatted' => '$365M',
                    'lastPrice' => 0.473,
                    'priceChangePercent' => -1.20,
                    'baseAsset' => 'ADA',
                    'quoteAsset' => 'USDT'
                ],
                [
                    'symbol' => 'XRPUSDT',
                    'name' => 'XRP',
                    'price' => 0.568,
                    'priceFormatted' => '$0.568',
                    'change24h' => 0.95,
                    'change24hFormatted' => '+0.95%',
                    'marketCap' => 31400000000,
                    'marketCapFormatted' => '$31.4B',
                    'volume24h' => 920000000,
                    'volume24hFormatted' => '$920M',
                    'lastPrice' => 0.568,
                    'priceChangePercent' => 0.95,
                    'baseAsset' => 'XRP',
                    'quoteAsset' => 'USDT'
                ],
                [
                    'symbol' => 'DOGEUSDT',
                    'name' => 'Dogecoin',
                    'price' => 0.097,
                    'priceFormatted' => '$0.097',
                    'change24h' => -0.75,
                    'change24hFormatted' => '-0.75%',
                    'marketCap' => 13800000000,
                    'marketCapFormatted' => '$13.8B',
                    'volume24h' => 450000000,
                    'volume24hFormatted' => '$450M',
                    'lastPrice' => 0.097,
                    'priceChangePercent' => -0.75,
                    'baseAsset' => 'DOGE',
                    'quoteAsset' => 'USDT'
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $fallbackData,
                'is_fallback' => true
            ]);
        }
    }
    
    /**
     * Get candlestick data for crypto
     */
    public function getCryptoCandles(Request $request)
    {
        $symbol = $request->input('symbol', 'BTCUSDT');
        $interval = $request->input('interval', '1h');
        $limit = (int) $request->input('limit', 100);
        
        try {
            $tradingApiService = app(TradingApiService::class);
            $candles = $tradingApiService->getCryptoCandles($symbol, $interval, $limit);
            
            return response()->json([
                'success' => true,
                'symbol' => $symbol,
                'interval' => $interval,
                'data' => $candles
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching crypto candles: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch candle data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get market status (simplified version)
     */
    protected function getMarketStatus()
    {
        // Check current time in NY timezone
        $now = new \DateTime('now', new \DateTimeZone('America/New_York'));
        $hour = (int)$now->format('G'); // 24-hour format without leading zeros
        $minute = (int)$now->format('i');
        $weekday = (int)$now->format('N'); // 1 (Monday) to 7 (Sunday)
        
        // Weekend
        if ($weekday >= 6) {
            return [
                'status' => 'closed',
                'nextOpen' => 'Monday, 9:30 AM ET'
            ];
        }
        
        // Before opening (9:30 AM)
        if ($hour < 9 || ($hour == 9 && $minute < 30)) {
            return [
                'status' => 'pre-market',
                'openTime' => '9:30 AM ET'
            ];
        }
        
        // After closing (4:00 PM)
        if ($hour >= 16) {
            return [
                'status' => 'closed',
                'nextOpen' => $weekday == 5 ? 'Monday, 9:30 AM ET' : 'Tomorrow, 9:30 AM ET'
            ];
        }
        
        // During market hours
        return [
            'status' => 'open',
            'closeTime' => '4:00 PM ET'
        ];
    }
    
    /**
     * Get sample stock data for fallback
     */
    protected function getSampleStockData($symbol)
    {
        // Predefined data for common stocks
        $stockData = [
            'AAPL' => ['price' => 148.56, 'name' => 'Apple Inc.'],
            'MSFT' => ['price' => 326.78, 'name' => 'Microsoft Corporation'],
            'GOOG' => ['price' => 138.45, 'name' => 'Alphabet Inc.'],
            'AMZN' => ['price' => 132.21, 'name' => 'Amazon.com, Inc.'],
            'META' => ['price' => 298.76, 'name' => 'Meta Platforms, Inc.'],
            'TSLA' => ['price' => 254.93, 'name' => 'Tesla, Inc.'],
            'NVDA' => ['price' => 413.02, 'name' => 'NVIDIA Corporation'],
        ];
        
        $basePrice = $stockData[$symbol]['price'] ?? 100.00;
        
        return [
            'symbol' => $symbol,
            'name' => $stockData[$symbol]['name'] ?? "{$symbol} Inc.",
            'price' => $basePrice,
            'change' => rand(-100, 100) / 100, // Random change +/- $1.00
            'changePercent' => rand(-300, 300) / 100, // Random change +/- 3.00%
            'volume' => rand(1000000, 10000000),
            'latestTradingDay' => date('Y-m-d'),
            'open' => $basePrice * (1 - (rand(0, 50) / 1000)), // Slightly lower than price
            'high' => $basePrice * (1 + (rand(0, 100) / 1000)), // Slightly higher than price
            'low' => $basePrice * (1 - (rand(50, 150) / 1000)), // Slightly lower than open
            'prevClose' => $basePrice * (1 + (rand(-100, 100) / 1000)), // Random close
            'is_fallback' => true
        ];
    }
    
    /**
     * Get list of top popular stock symbols
     */
    public function getStockSymbols(Request $request)
    {
        $exchange = $request->query('exchange', 'US');
        
        // Define the top popular stocks
        $popularStocks = [
            'AAPL' => 'Apple Inc.',
            'MSFT' => 'Microsoft Corporation',
            'GOOG' => 'Alphabet Inc. Class C',
            'GOOGL' => 'Alphabet Inc. Class A',
            'AMZN' => 'Amazon.com Inc.',
            'META' => 'Meta Platforms Inc.',
            'TSLA' => 'Tesla Inc.',
            'NVDA' => 'NVIDIA Corporation',
            'JPM' => 'JPMorgan Chase & Co.',
            'V' => 'Visa Inc.',
            'WMT' => 'Walmart Inc.',
            'BAC' => 'Bank of America Corp.',
            'PG' => 'Procter & Gamble Co.',
            'MA' => 'Mastercard Inc.',
            'XOM' => 'Exxon Mobil Corporation',
            'NFLX' => 'Netflix Inc.',
            'DIS' => 'Walt Disney Co.',
            'CSCO' => 'Cisco Systems Inc.',
            'INTC' => 'Intel Corporation',
            'ADBE' => 'Adobe Inc.'
        ];
        
        try {
            $tradingApiService = app(TradingApiService::class);
            
            // Try to get real data from Finnhub first
            $apiSymbols = $tradingApiService->getStockSymbolsFinnhub($exchange);
            
            // If successful, prioritize popular stocks and keep only real symbols from the list
            if (!empty($apiSymbols)) {
                $formattedSymbols = [];
                
                // First add all popular stocks that exist in the API response
                foreach ($popularStocks as $symbol => $description) {
                    // Find the symbol in API response
                    $found = false;
                    foreach ($apiSymbols as $apiSymbol) {
                        if ($apiSymbol['symbol'] === $symbol) {
                            $formattedSymbols[] = [
                                'symbol' => $symbol,
                                'description' => $apiSymbol['description'] ?? $description
                            ];
                            $found = true;
                            break;
                        }
                    }
                    
                    // If not found in API but it's popular, add with our description
                    if (!$found) {
                        $formattedSymbols[] = [
                            'symbol' => $symbol,
                            'description' => $description
                        ];
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'data' => $formattedSymbols
                ]);
            }
            
            // Fallback to our defined list if API returned empty results
            throw new \Exception("No symbols returned from API");
            
        } catch (\Exception $e) {
            Log::error('Error fetching stock symbols: ' . $e->getMessage());
            
            // Format our popular stocks for response
            $fallbackSymbols = [];
            foreach ($popularStocks as $symbol => $description) {
                $fallbackSymbols[] = [
                    'symbol' => $symbol,
                    'description' => $description
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $fallbackSymbols,
                'is_fallback' => true
            ]);
        }
    }
} 