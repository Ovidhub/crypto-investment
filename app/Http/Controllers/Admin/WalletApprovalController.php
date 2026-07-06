<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConnectedWallet;
use App\Models\User;
use Illuminate\Http\Request;

class WalletApprovalController extends Controller
{
    public function index()
    {
        $wallets = ConnectedWallet::with('user')->latest()->get();

        return view('admin.wallet-approvals', [
            'wallets' => $wallets,
            'pending' => $wallets->where('status', 'pending')->count(),
        ]);
    }

    public function approve($id)
    {
        $wallet = ConnectedWallet::findOrFail($id);
        $wallet->update(['status' => 'approved']);

        return back()->with('success', 'Wallet approved.');
    }

    public function reject($id)
    {
        $wallet = ConnectedWallet::findOrFail($id);
        $wallet->update(['status' => 'rejected']);

        return back()->with('success', 'Wallet rejected.');
    }

    public function destroy($id)
    {
        ConnectedWallet::findOrFail($id)->delete();

        return back()->with('success', 'Wallet deleted.');
    }

    public function updateReward(Request $request, $userId)
    {
        $request->validate([
            'wallet_daily_reward' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($userId);
        $user->wallet_daily_reward = $request->wallet_daily_reward;
        $user->save();

        return back()->with('success', 'Daily reward updated for ' . ($user->username ?? $user->name) . '.');
    }
}
