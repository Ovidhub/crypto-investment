<?php

namespace App\Services;

use App\Models\TradingMarketDataCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TradingApiService
{
    /**
     * API endpoints
     */
    protected $huobiEndpoint = 'https://api.huobi.pro';
    protected $cryptoCompareEndpoint = 'https://min-api.cryptocompare.com/data';

    /**
     * API keys
     */
    protected $cryptoCompareApiKey;

    /**
     * Finnhub API
     */
    protected $finnhubEndpoint;
    protected $finnhubApiKey;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Load API keys from environment variables
        $this->cryptoCompareApiKey = env('CRYPTOCOMPARE_API_KEY', '');

        // Set up Finnhub API
        $this->finnhubEndpoint = config('services.finnhub.endpoint', 'https://finnhub.io/api/v1');
        $this->finnhubApiKey = config('services.finnhub.key');
    }

    /**
     * Get cryptocurrency price data from Huobi
     */
    public function getHuobiTicker($symbol)
    {
        // Convert Binance format (BTCUSDT) to Huobi format (btcusdt)
        $huobiSymbol = strtolower($symbol);
        $cacheKey = "huobi_ticker_{$symbol}";

        // Check cache first
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $response = Http::get("{$this->huobiEndpoint}/market/detail/merged", [
                'symbol' => $huobiSymbol
            ]);

            if ($response->successful() && isset($response->json()['status']) && $response->json()['status'] === 'ok') {
                $huobiData = $response->json()['tick'];

                // Get 24hr data for this symbol
                $detailResponse = Http::get("{$this->huobiEndpoint}/market/detail", [
                    'symbol' => $huobiSymbol
                ]);

                $detail = [];
                if ($detailResponse->successful() && isset($detailResponse->json()['status']) && $detailResponse->json()['status'] === 'ok') {
                    $detail = $detailResponse->json()['tick'];
                }

                // Format data to match our expected format
                $data = [
                    'symbol' => $symbol,
                    'lastPrice' => $huobiData['close'],
                    'priceChange' => $huobiData['close'] - $huobiData['open'],
                    'priceChangePercent' => round(($huobiData['close'] - $huobiData['open']) / $huobiData['open'] * 100, 2),
                    'highPrice' => $huobiData['high'],
                    'lowPrice' => $huobiData['low'],
                    'volume' => $huobiData['vol'],
                    'quoteVolume' => $detail['amount'] ?? 0,
                    'openPrice' => $huobiData['open'],
                    'source' => 'huobi'
                ];

                // Cache the data for 1 minute
                TradingMarketDataCache::store($cacheKey, $data, now()->addMinutes(1));

                return $data;
            } else {
                // Log the error
                Log::error("Failed to get Huobi ticker", [
                    'symbol' => $symbol,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Use fallback API if Huobi is restricted
                return $this->getCryptoCompareTicker($symbol);
            }
        } catch (\Exception $e) {
            Log::error("Exception fetching Huobi ticker", [
                'symbol' => $symbol,
                'error' => $e->getMessage()
            ]);

            // Use fallback API if Huobi is not available
            return $this->getCryptoCompareTicker($symbol);
        }
    }

    /**
     * Get cryptocurrency price data from CryptoCompare (fallback API)
     */
    public function getCryptoCompareTicker($symbol)
    {
        // Extract base and quote currencies from symbol (e.g., BTCUSDT -> BTC, USDT)
        $baseCurrency = substr($symbol, 0, -4); // Remove last 4 chars which are usually USDT
        $quoteCurrency = substr($symbol, -4);   // Get the quote currency (usually USDT)

        if (strlen($baseCurrency) < 2) {
            $baseCurrency = substr($symbol, 0, -3); // Try with 3 chars (e.g., ETHBTC)
            $quoteCurrency = substr($symbol, -3);
        }

        $cacheKey = "cryptocompare_ticker_{$symbol}";

        // Check cache first
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $response = Http::get("{$this->cryptoCompareEndpoint}/price", [
                'fsym' => $baseCurrency,
                'tsyms' => $quoteCurrency,
                'api_key' => $this->cryptoCompareApiKey
            ]);

            if ($response->successful()) {
                $priceData = $response->json();

                // Also get 24h data for percentage change
                $histResponse = Http::get("{$this->cryptoCompareEndpoint}/v2/histoday", [
                    'fsym' => $baseCurrency,
                    'tsym' => $quoteCurrency,
                    'limit' => 1,
                    'api_key' => $this->cryptoCompareApiKey
                ]);

                $histData = $histResponse->json();
                $prevPrice = $histData['Data']['Data'][0]['close'] ?? null;
                $currentPrice = $priceData[$quoteCurrency] ?? 0;

                // Format the data to match our expected format
                $formattedData = [
                    'symbol' => $symbol,
                    'lastPrice' => $currentPrice,
                    'priceChange' => $prevPrice ? ($currentPrice - $prevPrice) : 0,
                    'priceChangePercent' => $prevPrice ? (($currentPrice - $prevPrice) / $prevPrice * 100) : 0,
                    'volume' => $histData['Data']['Data'][0]['volumeto'] ?? 0,
                    'highPrice' => $histData['Data']['Data'][0]['high'] ?? $currentPrice,
                    'lowPrice' => $histData['Data']['Data'][0]['low'] ?? $currentPrice,
                    'openPrice' => $histData['Data']['Data'][0]['open'] ?? $currentPrice,
                    'source' => 'cryptocompare'
                ];

                // Cache the data for 1 minute
                TradingMarketDataCache::store($cacheKey, $formattedData, now()->addMinutes(1));

                return $formattedData;
            } else {
                Log::error("Failed to get CryptoCompare ticker", [
                    'symbol' => $symbol,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Return a minimal fallback dataset with dummy data
                return $this->getDummyTickerData($symbol);
            }
        } catch (\Exception $e) {
            Log::error("Exception fetching CryptoCompare ticker", [
                'symbol' => $symbol,
                'error' => $e->getMessage()
            ]);

            // Return a minimal fallback dataset with dummy data
            return $this->getDummyTickerData($symbol);
        }
    }

    /**
     * Generate dummy ticker data as a last resort
     */
    private function getDummyTickerData($symbol)
    {
        return [
            'symbol' => $symbol,
            'lastPrice' => $symbol == 'BTCUSDT' ? 30000 : ($symbol == 'ETHUSDT' ? 2000 : 1),
            'priceChange' => 0,
            'priceChangePercent' => 0,
            'volume' => 0,
            'highPrice' => $symbol == 'BTCUSDT' ? 30100 : ($symbol == 'ETHUSDT' ? 2010 : 1.1),
            'lowPrice' => $symbol == 'BTCUSDT' ? 29900 : ($symbol == 'ETHUSDT' ? 1990 : 0.9),
            'openPrice' => $symbol == 'BTCUSDT' ? 29950 : ($symbol == 'ETHUSDT' ? 1995 : 1),
            'source' => 'dummy_data',
            'is_fallback' => true
        ];
    }

    /**
     * Get cryptocurrency kline/candlestick data from Huobi
     */
    public function getCryptoCandles($symbol, $interval = '1h', $limit = 100)
    {
        // Convert Binance intervals to Huobi intervals
        $intervalMapping = [
            '1m' => '1min',
            '5m' => '5min',
            '15m' => '15min',
            '30m' => '30min',
            '1h' => '60min',
            '4h' => '4hour',
            '1d' => '1day',
            '1w' => '1week',
            '1M' => '1mon'
        ];

        $huobiInterval = $intervalMapping[$interval] ?? '60min';
        $huobiSymbol = strtolower($symbol);

        $cacheKey = "huobi_candles_{$symbol}_{$interval}_{$limit}";

        // Check cache first (short cache time for candles - 30 seconds)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $response = Http::get("{$this->huobiEndpoint}/market/history/kline", [
                'symbol' => $huobiSymbol,
                'period' => $huobiInterval,
                'size' => min($limit, 2000) // Huobi has a max limit of 2000
            ]);

            if ($response->successful() && isset($response->json()['status']) && $response->json()['status'] === 'ok') {
                $huobiData = $response->json()['data'];

                // Format data for trading view chart
                $formattedData = [];

                foreach ($huobiData as $candle) {
                    $formattedData[] = [
                        'time' => $candle['id'], // Unix timestamp
                        'open' => $candle['open'],
                        'high' => $candle['high'],
                        'low' => $candle['low'],
                        'close' => $candle['close'],
                        'volume' => $candle['vol']
                    ];
                }

                // Reverse to get ascending order (oldest to newest)
                $formattedData = array_reverse($formattedData);

                // Cache for 30 seconds
                TradingMarketDataCache::store($cacheKey, $formattedData, now()->addSeconds(30));

                return $formattedData;
            } else {
                Log::error('Failed to get Huobi candles', [
                    'symbol' => $symbol,
                    'interval' => $interval,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting Huobi candles: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top cryptocurrencies
     */
    public function getTopCryptos($limit = 10)
    {
        $cacheKey = "top_cryptos_{$limit}";

        // Check cache first (5 minutes for top cryptos)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            // Use CryptoCompare for top cryptos
            $response = Http::get("{$this->cryptoCompareEndpoint}/top/mktcapfull", [
                'limit' => $limit,
                'tsym' => 'USD',
                'api_key' => $this->cryptoCompareApiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $topCryptos = [];

                if (isset($data['Data'])) {
                    foreach ($data['Data'] as $crypto) {
                        $info = $crypto['CoinInfo'] ?? [];
                        $raw = $crypto['RAW']['USD'] ?? [];
                        $display = $crypto['DISPLAY']['USD'] ?? [];

                        $topCryptos[] = [
                            'symbol' => $info['Name'] ?? '',
                            'name' => $info['FullName'] ?? '',
                            'price' => $raw['PRICE'] ?? 0,
                            'priceFormatted' => $display['PRICE'] ?? '$0',
                            'change24h' => $raw['CHANGEPCT24HOUR'] ?? 0,
                            'change24hFormatted' => $display['CHANGEPCT24HOUR'] ?? '0%',
                            'marketCap' => $raw['MKTCAP'] ?? 0,
                            'marketCapFormatted' => $display['MKTCAP'] ?? '$0',
                            'volume24h' => $raw['VOLUME24HOUR'] ?? 0,
                            'volume24hFormatted' => $display['VOLUME24HOUR'] ?? '$0',
                            'imageUrl' => "https://www.cryptocompare.com{$info['ImageUrl']}"
                        ];
                    }
                }

                // Cache for 5 minutes
                TradingMarketDataCache::store($cacheKey, $topCryptos, now()->addMinutes(5));

                return $topCryptos;
            } else {
                Log::error('Failed to get top cryptos', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [];
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting top cryptos: ' . $e->getMessage());
            return [];
        }
    }

    /**
 * Calculate proper volume for a stock symbol using available data
 *
 * @param string $symbol The stock symbol
 * @return int The calculated volume
 */
private function calculateVolume($symbol)
{
    try {
        // Try the quote endpoint first as it's usually included in free plans
        $response = Http::withHeaders([
            'X-Finnhub-Token' => $this->finnhubApiKey
        ])->get("{$this->finnhubEndpoint}/quote", [
            'symbol' => $symbol
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Some Finnhub plans might include volume in the quote response
            if (isset($data['v']) && $data['v'] > 0) {
                return $data['v'];
            }

            // If no volume in quote, try company profile as it sometimes has trading volume
            $profileResponse = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/profile2", [
                'symbol' => $symbol
            ]);

            if ($profileResponse->successful()) {
                $profileData = $profileResponse->json();
                if (isset($profileData['shareOutstanding']) && $profileData['shareOutstanding'] > 0) {
                    // Estimate volume as a percentage of outstanding shares
                    // Typically 0.5-5% of outstanding shares trade daily
                    $volumeEstimate = round($profileData['shareOutstanding'] * 0.02 * 1000000);
                    return $volumeEstimate > 1000 ? $volumeEstimate : 1000000;
                }
            }

            // If we have price change data, estimate volume based on volatility
            if (isset($data['c']) && isset($data['d']) && $data['c'] > 0) {
                // Higher volatility (price change) generally correlates with higher volume
                $priceVolatility = abs($data['d'] / $data['c']);

                // Base estimate on symbol market cap tier
                $baseVolume = $this->getBaseVolumeForSymbol($symbol);

                // Adjust volume by volatility factor
                $volatilityMultiplier = 1 + ($priceVolatility * 10);
                $estimatedVolume = round($baseVolume * $volatilityMultiplier);

                return $estimatedVolume;
            }
        }

        // Fall back to predefined volumes if API methods fail
        return $this->getBaseVolumeForSymbol($symbol);

    } catch (\Exception $e) {
        Log::error('Error calculating volume: ' . $e->getMessage());
        return $this->getBaseVolumeForSymbol($symbol);
    }
}

/**
 * Get base volume estimate for common symbols
 *
 * @param string $symbol The stock symbol
 * @return int Base volume estimate
 */
private function getBaseVolumeForSymbol($symbol)
{
    // Common stock daily volume estimates (approximate)
    $stockVolumes = [
        'AAPL' => 70000000,
        'MSFT' => 25000000,
        'GOOG' => 1500000,
        'GOOGL' => 1500000,
        'AMZN' => 4000000,
        'META' => 20000000,
        'TSLA' => 100000000,
        'NVDA' => 40000000,
        'JNJ' => 8000000,
        'JPM' => 10000000,
        'NFLX' => 5000000,
        'PYPL' => 12000000,
        'INTC' => 30000000,
        'AMD' => 50000000,
        'DIS' => 10000000,
        'BA' => 5000000,
        'V' => 8000000,
        'WMT' => 6000000,
        'KO' => 12000000,
        'PEP' => 5000000
    ];

    if (isset($stockVolumes[$symbol])) {
        return $stockVolumes[$symbol];
    }

    // If not in our predefined list, make a reasonable estimate based on first letter
    // (stocks starting with different letters often belong to different exchanges/sectors)
    $firstLetter = strtoupper(substr($symbol, 0, 1));

    switch ($firstLetter) {
        case 'A':
        case 'M':
        case 'T': return 15000000;
        case 'B':
        case 'C': return 8000000;
        case 'D':
        case 'E': return 5000000;
        case 'F':
        case 'G': return 7000000;
        case 'I':
        case 'N': return 10000000;
        case 'S': return 12000000;
        default: return 5000000;
    }
}

   /**
 * Get stock quote from Finnhub API with improved volume calculation
 */
public function getStockQuoteFinnhub($symbol)
{
    $cacheKey = "finnhub_quote_{$symbol}";

    // Check cache first (1 minute for stock quotes)
    if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
        return $cachedData;
    }

    try {
        // Make API call to Finnhub quote endpoint
        $response = Http::withHeaders([
            'X-Finnhub-Token' => $this->finnhubApiKey
        ])->get("{$this->finnhubEndpoint}/quote", [
            'symbol' => $symbol
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Check if we have valid data
            if (isset($data['c']) && $data['c'] > 0) {
                // Calculate volume using our method that avoids the candles endpoint
                $volume = $this->calculateVolume($symbol);

                $formattedQuote = [
                    'symbol' => $symbol,
                    'price' => floatval($data['c']), // Current price
                    'change' => floatval($data['d']), // Price change
                    'changePercent' => floatval($data['dp']), // Percentage change
                    'high' => floatval($data['h']), // High price of the day
                    'low' => floatval($data['l']), // Low price of the day
                    'open' => floatval($data['o']), // Open price of the day
                    'prevClose' => floatval($data['pc']), // Previous close price
                    'time' => $data['t'], // UNIX timestamp
                    'volume' => $volume, // Our calculated volume
                    'source' => 'finnhub'
                ];

                // Get company name (separate API call)
                $companyName = $this->getCompanyNameFinnhub($symbol);
                $formattedQuote['name'] = $companyName ?? "{$symbol} Inc.";

                // Cache for 1 minute
                TradingMarketDataCache::store($cacheKey, $formattedQuote, now()->addMinute());

                return $formattedQuote;
            } else {
                Log::error('Failed to get stock quote from Finnhub - invalid data', [
                    'symbol' => $symbol,
                    'response' => $data
                ]);

                // Use fallback data
                return $this->getDummyStockData($symbol);
            }
        } else {
            Log::error('Failed to get stock quote from Finnhub', [
                'symbol' => $symbol,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Use fallback data
            return $this->getDummyStockData($symbol);
        }
    } catch (\Exception $e) {
        Log::error('Exception when getting stock quote from Finnhub: ' . $e->getMessage());

        // Use fallback data
        return $this->getDummyStockData($symbol);
    }
}

    /**
     * Get dummy stock data for fallback
     */
    private function getDummyStockData($symbol)
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
            'time' => time(),
            'is_fallback' => true,
            'source' => 'fallback'
        ];
    }

    /**
     * Get company profile (name, industry, etc.) from Finnhub
     */
    public function getCompanyProfileFinnhub($symbol)
    {
        $cacheKey = "finnhub_profile_{$symbol}";

        // Check cache first (1 day for company profile)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $response = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/profile2", [
                'symbol' => $symbol
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check if we have valid data
                if (isset($data['name']) && !empty($data['name'])) {
                    $profile = [
                        'symbol' => $data['ticker'] ?? $symbol,
                        'name' => $data['name'] ?? "{$symbol} Inc.",
                        'description' => $data['finnhubIndustry'] ? "A company in the {$data['finnhubIndustry']} industry." : '',
                        'exchange' => $data['exchange'] ?? 'NASDAQ',
                        'currency' => $data['currency'] ?? 'USD',
                        'country' => $data['country'] ?? 'US',
                        'sector' => $data['finnhubIndustry'] ?? 'Technology',
                        'industry' => $data['finnhubIndustry'] ?? 'Technology',
                        'employees' => $data['employeeTotal'] ?? '',
                        'marketCap' => $data['marketCapitalization'] ? ($data['marketCapitalization'] * 1000000) : 0,
                        'logo' => $data['logo'] ?? '',
                        'weburl' => $data['weburl'] ?? '',
                        'ipo' => $data['ipo'] ?? '',
                        'source' => 'finnhub'
                    ];

                    // Format market cap as string with B/M suffix
                    if (isset($data['marketCapitalization'])) {
                        $marketCap = floatval($data['marketCapitalization']) * 1000000; // Finnhub returns market cap in millions
                        if ($marketCap >= 1000000000) {
                            $profile['marketCapFormatted'] = '$' . number_format($marketCap / 1000000000, 2) . 'B';
                        } else if ($marketCap >= 1000000) {
                            $profile['marketCapFormatted'] = '$' . number_format($marketCap / 1000000, 2) . 'M';
                        } else {
                            $profile['marketCapFormatted'] = '$' . number_format($marketCap, 2);
                        }
                    }

                    // Cache for 1 day (company profile doesn't change frequently)
                    TradingMarketDataCache::store($cacheKey, $profile, now()->addDay());

                    return $profile;
                } else {
                    Log::warning('Failed to get company profile from Finnhub - empty data', [
                        'symbol' => $symbol,
                        'response' => $data
                    ]);

                    // Fall back to Finnhub overview
                    return $this->getCompanyOverview($symbol);
                }
            } else {
                Log::error('Failed to get company profile from Finnhub', [
                    'symbol' => $symbol,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Fall back to Finnhub overview
                return $this->getCompanyOverview($symbol);
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting company profile from Finnhub: ' . $e->getMessage());

            // Fall back to Finnhub overview
            return $this->getCompanyOverview($symbol);
        }
    }

    /**
     * Get company name from Finnhub (helper method)
     */
    private function getCompanyNameFinnhub($symbol)
    {
        try {
            $cacheKey = "finnhub_company_name_{$symbol}";

            // Check cache first (1 week for company name)
            if ($cachedName = TradingMarketDataCache::get($cacheKey)) {
                return $cachedName;
            }

            $response = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/profile2", [
                'symbol' => $symbol
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['name']) && !empty($data['name'])) {
                    // Cache for 1 week (company names don't change often)
                    TradingMarketDataCache::store($cacheKey, $data['name'], now()->addWeek());
                    return $data['name'];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting company name from Finnhub: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get recent candles from Finnhub (helper method for volume calculation)
     */
    private function getRecentCandlesFinnhub($symbol, $resolution = 'D', $count = 1)
    {
        try {
            $to = time();
            $from = $to - (86400 * $count); // 86400 = seconds in a day

            $response = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/candle", [
                'symbol' => $symbol,
                'resolution' => $resolution,
                'from' => $from,
                'to' => $to
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['s']) && $data['s'] === 'ok') {
                    return $data;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting candles from Finnhub: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get company metrics (PE, EPS, etc.) from Finnhub
     */
    public function getCompanyMetricsFinnhub($symbol)
    {
        $cacheKey = "finnhub_metrics_{$symbol}";

        // Check cache first (1 day for company metrics)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $response = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/metric", [
                'symbol' => $symbol,
                'metric' => 'all'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Check if we have valid metrics data
                if (isset($data['metric'])) {
                    $metrics = $data['metric'];

                    $formattedMetrics = [
                        'symbol' => $symbol,
                        'peRatio' => $metrics['peBasicExclExtraTTM'] ?? 0,
                        'eps' => $metrics['epsBasicExclExtraItemsTTM'] ?? 0,
                        'dividendYield' => $metrics['dividendYieldIndicatedAnnual'] ?? 0,
                        '52WeekHigh' => $metrics['52WeekHigh'] ?? 0,
                        '52WeekLow' => $metrics['52WeekLow'] ?? 0,
                        'beta' => $metrics['beta'] ?? 0,
                        'marketCap' => $metrics['marketCapitalization'] ?? 0,
                        'priceToSalesRatio' => $metrics['psAnnual'] ?? 0,
                        'priceToBookRatio' => $metrics['pbAnnual'] ?? 0,
                        'revenueGrowth' => $metrics['revenueGrowth5Y'] ?? 0,
                        'epsGrowth' => $metrics['epsGrowth5Y'] ?? 0,
                        'totalDebtEquity' => $metrics['totalDebtEquity'] ?? 0,
                        'source' => 'finnhub'
                    ];

                    // Format metrics for display
                    if (isset($metrics['peBasicExclExtraTTM']) && $metrics['peBasicExclExtraTTM'] > 0) {
                        $formattedMetrics['peRatioFormatted'] = number_format($metrics['peBasicExclExtraTTM'], 2);
                    } else {
                        $formattedMetrics['peRatioFormatted'] = 'N/A';
                    }

                    if (isset($metrics['epsBasicExclExtraItemsTTM'])) {
                        $formattedMetrics['epsFormatted'] = '$' . number_format($metrics['epsBasicExclExtraItemsTTM'], 2);
                    } else {
                        $formattedMetrics['epsFormatted'] = 'N/A';
                    }

                    if (isset($metrics['dividendYieldIndicatedAnnual']) && $metrics['dividendYieldIndicatedAnnual'] > 0) {
                        $formattedMetrics['dividendYieldFormatted'] = number_format($metrics['dividendYieldIndicatedAnnual'] * 100, 2) . '%';
                    } else {
                        $formattedMetrics['dividendYieldFormatted'] = 'N/A';
                    }

                    if (isset($metrics['52WeekHigh']) && isset($metrics['52WeekLow'])) {
                        $formattedMetrics['52WeekRange'] = '$' . number_format($metrics['52WeekLow'], 2) . ' - $' . number_format($metrics['52WeekHigh'], 2);
                    } else {
                        $formattedMetrics['52WeekRange'] = 'N/A';
                    }

                    // Cache for 1 day
                    TradingMarketDataCache::store($cacheKey, $formattedMetrics, now()->addDay());

                    return $formattedMetrics;
                } else {
                    Log::warning('Failed to get company metrics from Finnhub - invalid data', [
                        'symbol' => $symbol,
                        'response' => $data
                    ]);

                    // Fall back to Finnhub overview
                    return $this->getCompanyOverview($symbol);
                }
            } else {
                Log::error('Failed to get company metrics from Finnhub', [
                    'symbol' => $symbol,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Fall back to Finnhub overview
                return $this->getCompanyOverview($symbol);
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting company metrics from Finnhub: ' . $e->getMessage());

            // Fall back to Finnhub overview
            return $this->getCompanyOverview($symbol);
        }
    }

    /**
     * Get stock candle data from Finnhub
     */
    public function getStockCandlesFinnhub($symbol, $resolution = 'D', $days = 30)
    {
        $cacheKey = "finnhub_candles_{$symbol}_{$resolution}_{$days}";

        // Check cache first (5 minutes for candle data)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $to = time();
            $from = $to - (86400 * $days); // 86400 = seconds in a day

            $response = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/candle", [
                'symbol' => $symbol,
                'resolution' => $resolution,
                'from' => $from,
                'to' => $to
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['s']) && $data['s'] === 'ok') {
                    $formattedCandles = [];

                    // Convert data to format expected by charts
                    for ($i = 0; $i < count($data['t']); $i++) {
                        $formattedCandles[] = [
                            'time' => $data['t'][$i],
                            'open' => $data['o'][$i],
                            'high' => $data['h'][$i],
                            'low' => $data['l'][$i],
                            'close' => $data['c'][$i],
                            'volume' => $data['v'][$i]
                        ];
                    }

                    // Cache for 5 minutes
                    TradingMarketDataCache::store($cacheKey, $formattedCandles, now()->addMinutes(5));

                    return $formattedCandles;
                } else {
                    Log::warning('Failed to get stock candles from Finnhub - invalid data', [
                        'symbol' => $symbol,
                        'response' => $data
                    ]);

                    // Fall back to Finnhub overview
                    return $this->getCompanyOverview($symbol);
                }
            } else {
                Log::error('Failed to get stock candles from Finnhub', [
                    'symbol' => $symbol,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Fall back to Finnhub overview
                return $this->getCompanyOverview($symbol);
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting stock candles from Finnhub: ' . $e->getMessage());

            // Fall back to Finnhub overview
            return $this->getCompanyOverview($symbol);
        }
    }

    /**
     * Get stock symbols from Finnhub API
     */
    public function getStockSymbolsFinnhub($exchange = 'US')
    {
        $cacheKey = "finnhub_symbols_{$exchange}";

        // Check cache first (1 day for symbols list)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            $response = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/symbol", [
                'exchange' => $exchange
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data) && !empty($data)) {
                    // Extract relevant information and limit to most common stocks
                    $symbols = array_slice($data, 0, 500);

                    $formattedSymbols = array_map(function($item) {
                        return [
                            'symbol' => $item['symbol'] ?? '',
                            'description' => $item['description'] ?? '',
                            'type' => $item['type'] ?? '',
                            'currency' => $item['currency'] ?? 'USD',
                        ];
                    }, $symbols);

                    // Sort by symbol name
                    usort($formattedSymbols, function($a, $b) {
                        return strcmp($a['symbol'], $b['symbol']);
                    });

                    // Cache for 1 day (stock symbols don't change frequently)
                    TradingMarketDataCache::store($cacheKey, $formattedSymbols, now()->addDay());

                    return $formattedSymbols;
                } else {
                    Log::warning('Failed to get stock symbols from Finnhub - empty data', [
                        'exchange' => $exchange,
                        'response' => $data
                    ]);

                    return $this->getFallbackStockSymbols();
                }
            } else {
                Log::error('Failed to get stock symbols from Finnhub', [
                    'exchange' => $exchange,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return $this->getFallbackStockSymbols();
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting stock symbols from Finnhub: ' . $e->getMessage());

            return $this->getFallbackStockSymbols();
        }
    }

    /**
     * Get fallback stock symbols
     */
    private function getFallbackStockSymbols()
    {
        return [
            ['symbol' => 'AAPL', 'description' => 'Apple Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'MSFT', 'description' => 'Microsoft Corporation', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'GOOG', 'description' => 'Alphabet Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'GOOGL', 'description' => 'Alphabet Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'AMZN', 'description' => 'Amazon.com, Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'META', 'description' => 'Meta Platforms, Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'TSLA', 'description' => 'Tesla, Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'NVDA', 'description' => 'NVIDIA Corporation', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'JPM', 'description' => 'JPMorgan Chase & Co.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'V', 'description' => 'Visa Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'WMT', 'description' => 'Walmart Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'JNJ', 'description' => 'Johnson & Johnson', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'PG', 'description' => 'The Procter & Gamble Company', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'MA', 'description' => 'Mastercard Incorporated', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'UNH', 'description' => 'UnitedHealth Group Incorporated', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'HD', 'description' => 'The Home Depot, Inc.', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'BAC', 'description' => 'Bank of America Corporation', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'KO', 'description' => 'The Coca-Cola Company', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'DIS', 'description' => 'The Walt Disney Company', 'type' => 'Common Stock', 'currency' => 'USD'],
            ['symbol' => 'PFE', 'description' => 'Pfizer Inc.', 'type' => 'Common Stock', 'currency' => 'USD']
        ];
    }

    /**
     * Get company overview from Finnhub (combine profile and metrics)
     */
    public function getCompanyOverview($symbol)
    {
        $cacheKey = "finnhub_overview_{$symbol}";

        // Check cache first (1 day for company overview data as it rarely changes)
        if ($cachedData = TradingMarketDataCache::get($cacheKey)) {
            return $cachedData;
        }

        try {
            // Get company profile
            $profileResponse = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/profile2", [
                'symbol' => $symbol
            ]);

            // Get company metrics
            $metricsResponse = Http::withHeaders([
                'X-Finnhub-Token' => $this->finnhubApiKey
            ])->get("{$this->finnhubEndpoint}/stock/metric", [
                'symbol' => $symbol,
                'metric' => 'all'
            ]);

            if ($profileResponse->successful() && $metricsResponse->successful()) {
                $profile = $profileResponse->json();
                $metricsData = $metricsResponse->json();
                $metrics = $metricsData['metric'] ?? [];

                // Check if we have valid data
                if (!empty($profile) || !empty($metrics)) {
                    // Format the data combining both sources
                    $overview = [
                        'symbol' => $symbol,
                        'name' => $profile['name'] ?? "{$symbol} Inc.",
                        'description' => $profile['description'] ?? '',
                        'exchange' => $profile['exchange'] ?? '',
                        'currency' => $profile['currency'] ?? 'USD',
                        'country' => $profile['country'] ?? '',
                        'sector' => $profile['finnhubIndustry'] ?? '',
                        'industry' => $profile['finnhubIndustry'] ?? '',
                        'employees' => $profile['employeeTotal'] ?? '',
                        'marketCap' => $profile['marketCapitalization'] ?? $metrics['marketCapitalization'] ?? 0,
                        'peRatio' => $metrics['peBasicExclExtraTTM'] ?? 0,
                        'eps' => $metrics['epsBasicExclExtraItemsTTM'] ?? 0,
                        'dividendYield' => $metrics['dividendYieldIndicatedAnnual'] ?? 0,
                        '52WeekHigh' => $metrics['52WeekHigh'] ?? 0,
                        '52WeekLow' => $metrics['52WeekLow'] ?? 0,
                        'beta' => $metrics['beta'] ?? 0,
                        'profitMargin' => $metrics['netProfitMarginTTM'] ?? 0,
                        'quarterlyEarningsGrowth' => $metrics['epsGrowthQuarterlyYoy'] ?? 0,
                        'quarterlyRevenueGrowth' => $metrics['revenueGrowthQuarterlyYoy'] ?? 0,
                        'analystTargetPrice' => $metrics['targetMedianPrice'] ?? 0,
                        'trailingPE' => $metrics['peBasicExclExtraTTM'] ?? 0,
                        'forwardPE' => $metrics['forwardPE'] ?? 0,
                        'priceToSalesRatio' => $metrics['psAnnual'] ?? 0,
                        'priceToBookRatio' => $metrics['pbAnnual'] ?? 0,
                        'evToRevenue' => $metrics['evToSalesAnnual'] ?? 0,
                        'evToEBITDA' => $metrics['evToEbitdaAnnual'] ?? 0,
                        'bookValue' => $metrics['bookValuePerShareQuarterly'] ?? 0,
                        'dividendPerShare' => $metrics['dividendPerShareAnnual'] ?? 0,
                        'returnOnAssetsTTM' => $metrics['roaRfy'] ?? 0,
                        'returnOnEquityTTM' => $metrics['roeRfy'] ?? 0,
                        'revenuePerShareTTM' => $metrics['revenuePerShareTTM'] ?? 0,
                        'source' => 'finnhub'
                    ];

                    // Format market cap as string with B/M suffix
                    if (isset($overview['marketCap']) && $overview['marketCap'] > 0) {
                        $marketCap = floatval($overview['marketCap']);
                        if ($marketCap >= 1000000000) {
                            $overview['marketCapFormatted'] = '$' . number_format($marketCap / 1000000000, 2) . 'B';
                        } else if ($marketCap >= 1000000) {
                            $overview['marketCapFormatted'] = '$' . number_format($marketCap / 1000000, 2) . 'M';
                        } else {
                            $overview['marketCapFormatted'] = '$' . number_format($marketCap, 2);
                        }
                    }

                    // Format 52-week range
                    if (isset($overview['52WeekHigh']) && isset($overview['52WeekLow']) &&
                        $overview['52WeekHigh'] > 0 && $overview['52WeekLow'] > 0) {
                        $overview['52WeekRange'] = '$' . number_format(floatval($overview['52WeekLow']), 2) . ' - $' . number_format(floatval($overview['52WeekHigh']), 2);
                    }

                    // Format dividend yield as percentage
                    if (isset($overview['dividendYield']) && floatval($overview['dividendYield']) > 0) {
                        $overview['dividendYieldFormatted'] = number_format(floatval($overview['dividendYield']) * 100, 2) . '%';
                    } else {
                        $overview['dividendYieldFormatted'] = 'N/A';
                    }

                    // Format EPS with $ sign
                    if (isset($overview['eps']) && $overview['eps'] !== 0) {
                        $overview['epsFormatted'] = '$' . number_format(floatval($overview['eps']), 2);
                    } else {
                        $overview['epsFormatted'] = 'N/A';
                    }

                    // Format PE ratio
                    if (isset($overview['peRatio']) && floatval($overview['peRatio']) > 0) {
                        $overview['peRatioFormatted'] = number_format(floatval($overview['peRatio']), 2);
                    } else {
                        $overview['peRatioFormatted'] = 'N/A';
                    }

                    // Cache for 1 day
                    TradingMarketDataCache::store($cacheKey, $overview, now()->addDay());

                    return $overview;
                } else {
                    Log::error('Failed to get company overview from Finnhub - invalid data', [
                        'symbol' => $symbol,
                        'profile' => $profile,
                        'metrics' => $metrics
                    ]);

                    // Use fallback data
                    return $this->getDummyCompanyOverview($symbol);
                }
            } else {
                Log::error('Failed to get company overview from Finnhub', [
                    'symbol' => $symbol,
                    'profileStatus' => $profileResponse->status(),
                    'metricsStatus' => $metricsResponse->status()
                ]);

                // Use fallback data
                return $this->getDummyCompanyOverview($symbol);
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting company overview from Finnhub: ' . $e->getMessage());

            // Use fallback data
            return $this->getDummyCompanyOverview($symbol);
        }
    }

    /**
     * Get stock daily data (used as fallback for getStockCandlesFinnhub)
     */
    public function getStockDailyData($symbol, $outputSize = 'compact')
    {
        // This now simply redirects to the Finnhub implementation
        return $this->getStockCandlesFinnhub($symbol, 'D', $outputSize === 'full' ? 365 : 30);
    }

    /**
     * Get stock intraday data (alias for Finnhub candles with interval mapping)
     */
    public function getStockIntradayData($symbol, $interval = '5min', $outputSize = 'compact')
    {
        // Map Alpha Vantage intervals to Finnhub resolutions
        $resolutionMap = [
            '1min' => '1',
            '5min' => '5',
            '15min' => '15',
            '30min' => '30',
            '60min' => 'H',
            'day' => 'D',
            'week' => 'W',
            'month' => 'M'
        ];

        $resolution = $resolutionMap[$interval] ?? '5';

        // Calculate days based on interval and outputSize
        $days = 1;
        if ($outputSize === 'full') {
            if ($interval === '1min') $days = 7;
            else if ($interval === '5min') $days = 30;
            else if ($interval === '15min') $days = 60;
            else if ($interval === '30min' || $interval === '60min') $days = 90;
            else $days = 365;
        } else {
            if ($interval === '1min') $days = 1;
            else if ($interval === '5min') $days = 5;
            else if ($interval === '15min') $days = 15;
            else if ($interval === '30min') $days = 15;
            else if ($interval === '60min') $days = 30;
            else $days = 30;
        }

        return $this->getStockCandlesFinnhub($symbol, $resolution, $days);
    }
}
