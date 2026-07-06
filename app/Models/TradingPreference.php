<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingPreference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'category',
    ];

    /**
     * Get the user that owns the preference.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set a preference value for a user.
     */
    public static function setPreference($userId, $key, $value, $category = 'general')
    {
        $preference = self::firstOrNew([
            'user_id' => $userId,
            'key' => $key,
        ]);

        $preference->value = is_array($value) ? json_encode($value) : $value;
        $preference->category = $category;
        $preference->save();

        return $preference;
    }

    /**
     * Get a preference value for a user.
     */
    public static function getPreference($userId, $key, $default = null)
    {
        $preference = self::where('user_id', $userId)->where('key', $key)->first();

        if (!$preference) {
            return $default;
        }

        // Auto-detect and decode JSON values
        if (is_string($preference->value) && (
            str_starts_with(trim($preference->value), '{') ||
            str_starts_with(trim($preference->value), '[')
        )) {
            return json_decode($preference->value, true);
        }

        return $preference->value;
    }

    /**
     * Get all preferences for a user by category.
     */
    public static function getAllPreferences($userId, $category = null)
    {
        $query = self::where('user_id', $userId);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $preferences = $query->get();
        
        $result = [];
        foreach ($preferences as $preference) {
            // Auto-detect and decode JSON values
            $value = $preference->value;
            if (is_string($value) && (
                str_starts_with(trim($value), '{') ||
                str_starts_with(trim($value), '[')
            )) {
                $value = json_decode($value, true);
            }
            
            $result[$preference->key] = $value;
        }
        
        return $result;
    }
} 