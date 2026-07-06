<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Signal;
use App\Models\Wdmethod; // <--- Use your actual model here
use Illuminate\Support\Facades\Auth;

class SignalController extends Controller
{
    public function index()
    {
        return view('user.signals', [
            'title' => 'Premium Trading Signals',
            'signals' => \App\Models\Signal::all(),
            // Change this to all() to verify the connection is working
            'dmethods' => \App\Models\Wdmethod::all() 
        ]);
    }
}