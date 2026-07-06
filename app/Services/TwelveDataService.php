<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Settings;
use App\Models\TradingMarketDataCache;
use Carbon\Carbon;

class TwelveDataService
{
    protected $apiKey;
    protected $endpoint;
    protected $rateLimitDelay = 7500000; // 7.5 seconds (8 requests per minute)

    public function __construct()
    {
        $settings = Settings::first();
        $this->apiKey = $settings->twelvedata_api_key ?? env('TWELVEDATA_API_KEY');
        $this->endpoint = 'https://api.twelvedata.com';
    }

    /**
     * Get real-time stock quote
     * 
     * @param string $symbol Stock symbol (e.g., AAPL, MSFT)
     * @param bool $fetchLogo Whether to fetch logo URL (first time only)
     * @return array Stock quote data
     */
    public function getQuote($symbol, $fetchLogo = false)
    {
        $cacheKey = "twelvedata_quote_{$symbol}";
        
        // Check cache first (1 minute TTL for quotes)
        if ($cached = TradingMarketDataCache::get($cacheKey)) {
            return $cached;
        }

        try {
            // Rate limiting
            usleep($this->rateLimitDelay);

            $response = Http::get("{$this->endpoint}/quote", [
                'symbol' => $symbol,
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['code']) && $data['code'] != 200) {
                    Log::error('TwelveData API error', ['error' => $data]);
                    return $this->getFallbackQuote($symbol);
                }

                $formatted = [
                    'symbol' => $data['symbol'] ?? $symbol,
                    'name' => $data['name'] ?? $symbol,
                    'exchange' => $data['exchange'] ?? null,
                    'price' => floatval($data['close'] ?? $data['price'] ?? 0),
                    'open' => floatval($data['open'] ?? 0),
                    'high' => floatval($data['high'] ?? 0),
                    'low' => floatval($data['low'] ?? 0),
                    'previous_close' => floatval($data['previous_close'] ?? 0),
                    'change' => floatval($data['change'] ?? 0),
                    'percent_change' => floatval($data['percent_change'] ?? 0),
                    'volume' => intval($data['volume'] ?? 0),
                    'timestamp' => $data['timestamp'] ?? now()->timestamp,
                    'source' => 'twelvedata'
                ];

                // Fetch logo if requested
                if ($fetchLogo) {
                    $formatted['logo_url'] = $this->getLogo($symbol);
                }

                // Cache for 1 minute
                TradingMarketDataCache::store($cacheKey, $formatted, now()->addMinute());
                
                return $formatted;
            }

            Log::error('TwelveData quote failed', [
                'symbol' => $symbol,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return $this->getFallbackQuote($symbol);

        } catch (\Exception $e) {
            Log::error('TwelveData exception: ' . $e->getMessage());
            return $this->getFallbackQuote($symbol);
        }
    }

    /**
     * Get stock logo URL
     * 
     * @param string $symbol Stock symbol
     * @return string|null Logo URL or null
     */
    public function getLogo($symbol)
    {
        $cacheKey = "twelvedata_logo_{$symbol}";
        
        // Check cache first (7 days TTL for logos)
        if ($cached = TradingMarketDataCache::get($cacheKey)) {
            return $cached;
        }

        try {
            // Rate limiting
            usleep($this->rateLimitDelay);

            $response = Http::get("{$this->endpoint}/logo", [
                'symbol' => $symbol,
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $logoUrl = $data['url'] ?? null;

                if ($logoUrl && $this->validateLogoUrl($logoUrl)) {
                    // Cache for 7 days
                    TradingMarketDataCache::store($cacheKey, $logoUrl, now()->addDays(7));
                    return $logoUrl;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('TwelveData logo fetch error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Search for stocks by symbol or name
     * 
     * @param string $query Search query
     * @return array Search results
     */
    public function searchSymbol($query)
    {
        $cacheKey = "twelvedata_search_" . md5($query);
        
        // Check cache first (5 minutes TTL for search)
        if ($cached = TradingMarketDataCache::get($cacheKey)) {
            return $cached;
        }

        try {
            // Rate limiting
            usleep($this->rateLimitDelay);

            $response = Http::get("{$this->endpoint}/symbol_search", [
                'symbol' => $query,
                'apikey' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['data'] ?? [];

                // Cache for 5 minutes
                TradingMarketDataCache::store($cacheKey, $results, now()->addMinutes(5));
                
                return $results;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('TwelveData search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Validate if logo URL is accessible
     * 
     * @param string $url Logo URL
     * @return bool True if valid, false otherwise
     */
    public function validateLogoUrl($url)
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        try {
            $response = Http::timeout(5)->head($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if US stock market is open
     * 
     * @return bool True if market is open, false otherwise
     */
    public function isMarketOpen()
    {
        $settings = Settings::first();
        
        // If market hours check is disabled, always return true
        if ($settings->stock_market_hours_enabled !== 'on') {
            return true;
        }

        $now = Carbon::now('America/New_York');
        
        // Check if it's a weekend
        if ($now->isWeekend()) {
            return false;
        }

        // Check if it's a NYSE holiday
        if ($this->isMarketHoliday($now)) {
            return false;
        }

        // Check if it's during market hours (9:30 AM - 4:00 PM EST)
        $marketOpen = Carbon::createFromTime(9, 30, 0, 'America/New_York');
        $marketClose = Carbon::createFromTime(16, 0, 0, 'America/New_York');

        return $now->between($marketOpen, $marketClose);
    }

    /**
     * Check if a given date is a NYSE holiday
     * 
     * @param Carbon $date Date to check
     * @return bool True if holiday, false otherwise
     */
    public function isMarketHoliday($date)
    {
        $holidays = $this->getNYSEHolidays($date->year);
        $dateString = $date->format('Y-m-d');

        return in_array($dateString, $holidays);
    }

    /**
     * Get NYSE holidays for a given year (hardcoded)
     * 
     * @param int $year Year
     * @return array Array of holiday dates in Y-m-d format
     */
    public function getNYSEHolidays($year)
    {
        // Hardcoded NYSE holidays for 2026
        if ($year == 2026) {
            return [
                '2026-01-01', // New Year's Day
                '2026-01-19', // Martin Luther King Jr. Day
                '2026-02-16', // Presidents' Day
                '2026-04-03', // Good Friday
                '2026-05-25', // Memorial Day
                '2026-06-19', // Juneteenth (observed)
                '2026-07-03', // Independence Day (observed Friday)
                '2026-09-07', // Labor Day
                '2026-11-26', // Thanksgiving Day
                '2026-12-25', // Christmas Day
            ];
        }

        // For other years, calculate dynamically
        return $this->calculateNYSEHolidays($year);
    }

    /**
     * Calculate NYSE holidays dynamically for any year
     * 
     * @param int $year Year
     * @return array Array of holiday dates
     */
    private function calculateNYSEHolidays($year)
    {
        $holidays = [];

        // New Year's Day (if weekend, observe Monday)
        $newYear = Carbon::create($year, 1, 1);
        if ($newYear->isWeekend()) {
            $holidays[] = $newYear->next(Carbon::MONDAY)->format('Y-m-d');
        } else {
            $holidays[] = $newYear->format('Y-m-d');
        }

        // MLK Day (3rd Monday in January)
        $holidays[] = Carbon::create($year, 1, 1)->nthOfMonth(3, Carbon::MONDAY)->format('Y-m-d');

        // Presidents' Day (3rd Monday in February)
        $holidays[] = Carbon::create($year, 2, 1)->nthOfMonth(3, Carbon::MONDAY)->format('Y-m-d');

        // Good Friday (Friday before Easter)
        $easter = Carbon::createFromTimestamp(easter_date($year));
        $holidays[] = $easter->subDays(2)->format('Y-m-d');

        // Memorial Day (last Monday in May)
        $holidays[] = Carbon::create($year, 5, 31)->previous(Carbon::MONDAY)->format('Y-m-d');

        // Juneteenth (June 19, if weekend observe nearest weekday)
        $juneteenth = Carbon::create($year, 6, 19);
        if ($juneteenth->isSaturday()) {
            $holidays[] = $juneteenth->subDay()->format('Y-m-d');
        } elseif ($juneteenth->isSunday()) {
            $holidays[] = $juneteenth->addDay()->format('Y-m-d');
        } else {
            $holidays[] = $juneteenth->format('Y-m-d');
        }

        // Independence Day (July 4, if weekend observe nearest weekday)
        $independenceDay = Carbon::create($year, 7, 4);
        if ($independenceDay->isSaturday()) {
            $holidays[] = $independenceDay->subDay()->format('Y-m-d');
        } elseif ($independenceDay->isSunday()) {
            $holidays[] = $independenceDay->addDay()->format('Y-m-d');
        } else {
            $holidays[] = $independenceDay->format('Y-m-d');
        }

        // Labor Day (1st Monday in September)
        $holidays[] = Carbon::create($year, 9, 1)->nthOfMonth(1, Carbon::MONDAY)->format('Y-m-d');

        // Thanksgiving (4th Thursday in November)
        $holidays[] = Carbon::create($year, 11, 1)->nthOfMonth(4, Carbon::THURSDAY)->format('Y-m-d');

        // Christmas (December 25, if weekend observe nearest weekday)
        $christmas = Carbon::create($year, 12, 25);
        if ($christmas->isSaturday()) {
            $holidays[] = $christmas->subDay()->format('Y-m-d');
        } elseif ($christmas->isSunday()) {
            $holidays[] = $christmas->addDay()->format('Y-m-d');
        } else {
            $holidays[] = $christmas->format('Y-m-d');
        }

        return $holidays;
    }

    /**
     * Get fallback quote data when API fails
     * 
     * @param string $symbol Stock symbol
     * @return array Fallback quote data
     */
    private function getFallbackQuote($symbol)
    {
        return [
            'symbol' => $symbol,
            'name' => $symbol,
            'exchange' => 'UNKNOWN',
            'price' => 0,
            'open' => 0,
            'high' => 0,
            'low' => 0,
            'previous_close' => 0,
            'change' => 0,
            'percent_change' => 0,
            'volume' => 0,
            'timestamp' => now()->timestamp,
            'source' => 'fallback',
            'error' => true
        ];
    }
}
