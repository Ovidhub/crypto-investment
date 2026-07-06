<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoPrice extends Model
{
    protected $fillable = [
        'coin_symbol',
        'name',
        'logo',
        'price_usd',
        'percent_change_24h',
        'market_cap',
        'last_updated',
    ];

    public $timestamps = true;

    protected $casts = [
        'price_usd' => 'float',
        'percent_change_24h' => 'float',
        'market_cap' => 'float',
        'last_updated' => 'datetime',
    ];
}
