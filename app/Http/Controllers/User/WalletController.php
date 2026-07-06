<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ConnectedWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('user.wallet_connect', [
            'title'   => 'Connect Wallet',
            'wallets' => ConnectedWallet::where('user_id', $user->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|max:100',
            'network'  => 'nullable|string|max:100',
            // Public address is a single token (no spaces). This rule rejects a
            // pasted recovery/seed phrase, which we never want to store.
            'address'  => ['required', 'string', 'max:255', 'not_regex:/\s/'],
            'label'    => 'nullable|string|max:100',
        ], [
            'address.not_regex' => 'Enter a single PUBLIC wallet address. Never enter your recovery phrase or seed phrase.',
        ]);

        ConnectedWallet::create([
            'user_id'  => Auth::id(),
            'provider' => $request->provider,
            'network'  => $request->network,
            'address'  => $request->address,
            'label'    => $request->label,
            'status'   => 'pending',
        ]);

        return back()->with('success', 'Wallet submitted. It will show as connected once an admin approves it.');
    }

    public function destroy($id)
    {
        $wallet = ConnectedWallet::where('user_id', Auth::id())->findOrFail($id);
        $wallet->delete();

        return back()->with('success', 'Wallet removed.');
    }
}
