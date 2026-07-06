<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'enable_stock_trading',
        'twelvedata_api_key',
        'stock_market_hours_enabled',
        'max_stock_watchlist_items',
        'stock_trade_fee_percent',
        'last_stock_force_update_at',
        'enable_crypto_trading',
        'crypto_trade_fee_percent',
        'min_crypto_trade_amount',
        'max_crypto_watchlist_items',
    ];

    protected $casts = [
        'return_capital' => 'boolean',
        'should_cancel_plan' => 'boolean',
        'modules' => 'array'
    ];

    // public function getModulesAttribute($value)
    // {
    //     return ucfirst($value);
    // }
}