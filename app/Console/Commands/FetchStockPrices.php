<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TwelveDataService;
use App\Models\StockPrice;
use App\Models\Settings;
use App\Models\TradingWatchlistItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:fetch-prices {--force : Force update regardless of market hours} {--symbols= : Comma-separated list of symbols to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch stock prices from Twelve Data API and update database';

    protected $twelveDataService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TwelveDataService $twelveDataService)
    {
        parent::__construct();
        $this->twelveDataService = $twelveDataService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $settings = Settings::first();

        // Check if stock trading is enabled
        if ($settings->enable_stock_trading !== 'on') {
            $this->error('Stock trading is disabled in settings.');
            return 1;
        }

        // Check if API key is configured
        if (empty($settings->twelvedata_api_key)) {
            $this->error('Twelve Data API key is not configured.');
            return 1;
        }

        $isForced = $this->option('force');

        // Check market hours unless forced
        if (!$isForced && !$this->twelveDataService->isMarketOpen()) {
            $this->info('Market is currently closed. Use --force to update anyway.');
            return 0;
        }

        // If forced, check cooldown (5 minutes)
        if ($isForced) {
            $lastForceUpdate = $settings->last_stock_force_update_at;
            if ($lastForceUpdate) {
                $cooldownMinutes = 5;
                $nextAllowedUpdate = Carbon::parse($lastForceUpdate)->addMinutes($cooldownMinutes);
                
                if (Carbon::now()->lt($nextAllowedUpdate)) {
                    $remainingMinutes = Carbon::now()->diffInMinutes($nextAllowedUpdate, false);
                    $this->error("Force update on cooldown. Try again in " . abs($remainingMinutes) . " minutes.");
                    return 1;
                }
            }

            // Update last force update timestamp
            $settings->update(['last_stock_force_update_at' => now()]);
        }

        // Get symbols to update
        $symbols = $this->getSymbolsToUpdate();

        if (empty($symbols)) {
            $this->info('No stock symbols to update. Add stocks to watchlists first.');
            return 0;
        }

        $this->info('Fetching stock prices for ' . count($symbols) . ' symbols...');
        $bar = $this->output->createProgressBar(count($symbols));

        $successCount = 0;
        $errorCount = 0;

        foreach ($symbols as $symbol) {
            try {
                // Determine if this is first fetch (check if stock exists in DB)
                $existingStock = StockPrice::where('symbol', $symbol)->first();
                
                // Always fetch logo if it's missing
                $fetchLogo = !$existingStock || empty($existingStock->logo_url);

                // Get quote from Twelve Data
                $quote = $this->twelveDataService->getQuote($symbol, $fetchLogo);

                if (isset($quote['error']) && $quote['error']) {
                    $this->newLine();
                    $this->warn("Failed to fetch {$symbol}: API error");
                    $errorCount++;
                    $bar->advance();
                    continue;
                }

                // Update or create stock price record
                StockPrice::updateOrCreate(
                    ['symbol' => $quote['symbol']],
                    [
                        'name' => $quote['name'],
                        'exchange' => $quote['exchange'] ?? null,
                        'logo_url' => $quote['logo_url'] ?? ($existingStock->logo_url ?? null),
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

                $successCount++;
                $bar->advance();

            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error fetching {$symbol}: " . $e->getMessage());
                Log::error("Stock price fetch error for {$symbol}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errorCount++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Stock prices updated successfully!");
        $this->info("Success: {$successCount}, Errors: {$errorCount}");

        return 0;
    }

    /**
     * Get symbols to update based on options or watchlists
     *
     * @return array
     */
    private function getSymbolsToUpdate()
    {
        // If specific symbols provided via option
        if ($this->option('symbols')) {
            return array_map('trim', explode(',', $this->option('symbols')));
        }

        // Otherwise, get all symbols from stock watchlists
        return DB::table('trading_watchlist_items')
            ->join('trading_watchlists', 'trading_watchlist_items.watchlist_id', '=', 'trading_watchlists.id')
            ->where('trading_watchlists.type', 'stock')
            ->distinct()
            ->pluck('trading_watchlist_items.symbol')
            ->toArray();
    }
}

