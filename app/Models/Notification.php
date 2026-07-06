<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 
        'message', 
        'title',
        'type', 
        'is_read', 
        'link',
        'icon',
        'icon_bg_color'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
    ];
    
    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    // Scope for latest notifications
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
