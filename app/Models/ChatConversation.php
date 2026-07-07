<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $fillable = [
        'token', 'name', 'email', 'user_id', 'ip_address', 'country', 'country_code',
        'page_url', 'status', 'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }
}
