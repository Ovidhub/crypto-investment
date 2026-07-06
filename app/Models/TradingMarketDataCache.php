<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TradingMarketDataCache extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trading_market_data_cache';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'market_type',
        'data_type',
        'data',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Store data in the cache
     *
     * @param string $key Unique key for the cached item
     * @param mixed $data Data to cache (will be JSON encoded)
     * @param \Carbon\Carbon|null $expiresAt When the cache expires (null for no expiration)
     * @return \App\Models\TradingMarketDataCache
     */
    public static function store($key, $data, $expiresAt = null)
    {
        // Check if the key already exists
        $cache = self::where('key', $key)->first();
        
        if (!$cache) {
            $cache = new self();
            $cache->key = $key;
        }
        
        // Set default values for required fields
        $cache->market_type = $cache->market_type ?? 'general';
        $cache->data_type = $cache->data_type ?? 'json';
        $cache->data = json_encode($data);
        $cache->expires_at = $expiresAt;
        $cache->save();
        
        return $cache;
    }

    /**
     * Get cached data
     *
     * @param string $key Cache key
     * @return mixed|null The cached data or null if not found/expired
     */
    public static function get($key)
    {
        $cache = self::where('key', $key)
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();
        
        if ($cache) {
            return json_decode($cache->data, true);
        }
        
        return null;
    }

    /**
     * Check if valid cache exists
     *
     * @param string $key Cache key
     * @return bool
     */
    public static function hasValid($key)
    {
        return self::where('key', $key)
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Clean expired cache entries
     *
     * @return int Number of deleted records
     */
    public static function cleanExpired()
    {
        return self::where('expires_at', '<', now())->delete();
    }
} 