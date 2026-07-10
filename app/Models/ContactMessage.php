<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'message', 'ip_address', 'read_by_admin',
    ];

    protected $casts = [
        'read_by_admin' => 'boolean',
    ];
}
