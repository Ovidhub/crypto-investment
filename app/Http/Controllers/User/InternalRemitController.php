<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use DB;

class InternalRemitController extends Controller
{
    public function index() {
        return view('user.transfer', ['title' => 'Internal Transfer']);
    }

    public function processTransfer(Request $request) {
        $request->validate([
            'email' => 'required',
            'amount' => 'required|numeric|min:50',
        ]);

        $sender = Auth::user();
        // Finding receiver by email or username
        $receiver = User::where('email', $request->email)
                        ->orWhere('username', $request->email)
                        ->first();

        if (!$receiver) {
            return back()->with('danger', 'Recipient not found.');
        }

        if ($receiver->id == $sender->id) {
            return back()->with('danger', 'You cannot send money to yourself.');
        }

        if ($sender->account_bal < $request->amount) {
            return back()->with('danger', 'Insufficient balance.');
        }

        // Securely swap the funds
        DB::transaction(function () use ($sender, $receiver, $request) {
            $sender->decrement('account_bal', $request->amount);
            $receiver->increment('account_bal', $request->amount);
            
            // Note: You can add logic here to create a record in your 'tp_transactions' table
        });

        return back()->with('success', 'Transfer successful!');
    }
}