<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id', 'sender', 'body', 'read_by_admin', 'read_by_visitor',
    ];

    protected $casts = [
        'read_by_admin' => 'boolean',
        'read_by_visitor' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }
}
