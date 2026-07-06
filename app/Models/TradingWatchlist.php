<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingWatchlist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'is_default',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the user that owns the watchlist.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the watchlist.
     */
    public function items()
    {
        return $this->hasMany(TradingWatchlistItem::class, 'watchlist_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Get the favorite items for the watchlist.
     */
    public function favoriteItems()
    {
        return $this->hasMany(TradingWatchlistItem::class, 'watchlist_id')->where('is_favorite', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Create a default watchlist for a user if they don't have one.
     */
    public static function createDefaultForUser($userId, $type = 'crypto')
    {
        // Check if user already has a default watchlist of this type
        $existingDefault = self::where('user_id', $userId)
            ->where('type', $type)
            ->where('is_default', true)
            ->first();

        if (!$existingDefault) {
            return self::create([
                'user_id' => $userId,
                'name' => $type === 'crypto' ? 'My Crypto Watchlist' : 'My Stocks Watchlist',
                'type' => $type,
                'is_default' => true,
                'sort_order' => 0,
            ]);
        }

        return $existingDefault;
    }
} 