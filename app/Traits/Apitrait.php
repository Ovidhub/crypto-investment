<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait Apitrait
{
   
   
   
 public function get_rate($coin, $currency)
{
    try {
        $coinId = $this->mapCoinGeckoId(strtolower($coin));
        $vsCurrency = strtolower($currency);

        $response = Http::get("https://api.coingecko.com/api/v3/simple/price", [
            'ids' => $coinId,
            'vs_currencies' => $vsCurrency
        ]);

        $data = $response->json();

        if (isset($data[$coinId][$vsCurrency])) {
            return floatval($data[$coinId][$vsCurrency]);
        }

    } catch (\Exception $e) {
        \Log::error("CoinGecko error: " . $e->getMessage());
    }

    return 0; // return 0 if not found
}


private function mapCoinGeckoId($symbol)
{
    $map = [
    'btc' => 'bitcoin',
    'eth' => 'ethereum',
    'bnb' => 'binancecoin',
    'usdt' => 'tether',
    'usd' => 'usd',
    'ltc' => 'litecoin',
    'xrp' => 'ripple',
    'sol' => 'solana',
    'ada' => 'cardano',
    'link' => 'chainlink',    // LINK
    'aave' => 'aave',         // AAVE
    'bch' => 'bitcoin-cash', // BCH
    'xlm' => 'stellar',       // XLM
];

    return $map[$symbol] ?? $symbol;
}



}
