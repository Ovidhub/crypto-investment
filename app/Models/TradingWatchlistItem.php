<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingWatchlistItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'watchlist_id',
        'symbol',
        'market',
        'base_currency',
        'quote_currency',
        'sort_order',
        'is_favorite',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_favorite' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the watchlist that owns the item.
     */
    public function watchlist()
    {
        return $this->belongsTo(TradingWatchlist::class, 'watchlist_id');
    }

    /**
     * Get the stock price for this watchlist item.
     */
    public function stockPrice()
    {
        return $this->belongsTo(StockPrice::class, 'symbol', 'symbol');
    }

    /**
     * Get the crypto price for this watchlist item.
     */
    public function cryptoPrice()
    {
        return $this->belongsTo(CryptoPrice::class, 'symbol', 'coin_symbol');
    }

    /**
     * Add a symbol to a user's default watchlist.
     */
    public static function addToDefaultWatchlist($userId, $symbol, $type = 'crypto', $data = [])
    {
        // Get or create default watchlist
        $watchlist = TradingWatchlist::createDefaultForUser($userId, $type);

        // Check if symbol already exists in this watchlist
        $existingItem = self::where('watchlist_id', $watchlist->id)
            ->where('symbol', $symbol)
            ->first();

        if ($existingItem) {
            return $existingItem;
        }

        // Get highest sort order
        $maxSortOrder = self::where('watchlist_id', $watchlist->id)->max('sort_order') ?? 0;

        // Create new watchlist item with provided data
        $itemData = array_merge([
            'watchlist_id' => $watchlist->id,
            'symbol' => $symbol,
            'sort_order' => $maxSortOrder + 1,
            'is_favorite' => false,
        ], $data);

        return self::create($itemData);
    }

    /**
     * Get formatted symbol for display.
     */
    public function getFormattedSymbolAttribute()
    {
        $watchlist = $this->watchlist;
        
        if ($watchlist->type === 'crypto' && $this->base_currency && $this->quote_currency) {
            return $this->base_currency . '/' . $this->quote_currency;
        }
        
        return $this->symbol;
    }
} 