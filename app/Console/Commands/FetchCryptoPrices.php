<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FetchCryptoPrices extends Command
{
    protected $signature = 'crypto:fetch-prices';
    protected $description = 'Fetch live crypto prices from CoinGecko and store them in the database';

    public function handle(): void
    {
        $this->info("Fetching crypto prices...");

        try {
            $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => 50,
                'page' => 1,
                'sparkline' => false,
            ]);

            if ($response->failed()) {
                $this->error("Failed to fetch data from CoinGecko.");
                return;
            }

            $coins = $response->json();

            foreach ($coins as $coin) {
                DB::table('crypto_prices')->updateOrInsert(
                    ['coin_symbol' => strtoupper($coin['symbol'])],
                    [
                        'name' => $coin['name'],
                        'logo' => $coin['image'],
                        'price_usd' => $coin['current_price'],
                        'percent_change_24h' => $coin['price_change_percentage_24h'],
                        'market_cap' => $coin['market_cap'],
                        'last_updated' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            $this->info("Crypto prices updated successfully.");

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
