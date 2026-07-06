<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_plans extends Model
{
    use HasFactory;

    // This is the most important line - it forces the dash version
    protected $table = 'user_plans'; 

    protected $fillable = [
        'user', 
        'plan', 
        'amount', 
        'leverage', 
        'type', 
        'active', 
        'inv_duration', 
        'activated_at', 
        'profit_earned',
        'expire_date'
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'last_growth' => 'datetime',
        'expire_date' => 'datetime',
    ];

    // The user relationship (declared only ONCE)
    public function duser()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
    
    // The missing plan relationship we just added
    public function dplan()
    {
        return $this->belongsTo(\App\Models\User_plans::class, 'Plans', 'id');
    }
}