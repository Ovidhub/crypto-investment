<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'name',
        'exchange',
        'logo_url',
        'price',
        'open',
        'high',
        'low',
        'previous_close',
        'change',
        'percent_change',
        'volume',
        'market_cap',
        'pe_ratio',
        'dividend_yield',
        'last_updated',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'open' => 'decimal:4',
        'high' => 'decimal:4',
        'low' => 'decimal:4',
        'previous_close' => 'decimal:4',
        'change' => 'decimal:4',
        'percent_change' => 'decimal:4',
        'volume' => 'integer',
        'market_cap' => 'decimal:2',
        'pe_ratio' => 'decimal:2',
        'dividend_yield' => 'decimal:4',
        'last_updated' => 'datetime',
    ];

    /**
     * Get trades for this stock
     */
    public function trades()
    {
        return $this->hasMany(StockTrade::class, 'stock_symbol', 'symbol');
    }

    /**
     * Scope to get stocks in watchlist
     */
    public function scopeInWatchlist($query)
    {
        return $query->whereIn('symbol', function($query) {
            $query->select('symbol')
                ->from('trading_watchlist_items')
                ->join('trading_watchlists', 'trading_watchlist_items.watchlist_id', '=', 'trading_watchlists.id')
                ->where('trading_watchlists.type', 'stock');
        });
    }

    /**
     * Get placeholder SVG for stock logo
     */
    public function getPlaceholderSvg()
    {
        $firstLetter = strtoupper(substr($this->symbol, 0, 1));
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
            <defs>
                <linearGradient id="grad-' . $this->symbol . '" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#0F9D58;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#0F9D58;stop-opacity:0.8" />
                </linearGradient>
            </defs>
            <rect width="40" height="40" rx="8" fill="url(#grad-' . $this->symbol . ')" />
            <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="white" font-size="18" font-weight="bold" font-family="Arial, sans-serif">' . $firstLetter . '</text>
        </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get logo URL or placeholder
     */
    public function getLogoAttribute($value)
    {
        if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return $this->getPlaceholderSvg();
    }
}
