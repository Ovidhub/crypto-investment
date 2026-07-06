<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tp_Transaction extends Model
{
    use HasFactory;
    protected $fillable=['last_growth','plan','amount','type','user_plan_id','user'];
}
