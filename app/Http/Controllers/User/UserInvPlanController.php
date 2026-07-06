<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tp_Transaction;
use App\Models\User_plans;
use App\Mail\NewNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UserInvPlanController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle placing a new trade
     */
    public function joinplan(Request $request)
    {
        // 1. Validate incoming data
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'asset' => 'required|string',
            'order_type' => 'required|string',
            'leverage' => 'required',
        ]);

        $user = Auth::user();

        // 2. Check Balance
        if ($user->account_bal < $request->amount) {
            return back()->with('danger', 'Insufficient funds in your account balance.');
        }

        // 3. Process the Trade
        $trade = new User_plans();
        $trade->user = $user->id;
        $trade->amount = $request->amount;
        // CHANGED: 'assets' to 'plan' to match your database column
        $trade->plan = $request->asset; 
        $trade->type = $request->order_type; 
        $trade->leverage = $request->leverage;
        $trade->active = 'yes';
        $trade->inv_duration = $request->expire ?? '1 Hour';
        $trade->activated_at = Carbon::now();
        $trade->created_at = Carbon::now();
        $trade->save();

        // 4. Deduct Balance and Log Transaction
        $user->decrement('account_bal', $request->amount);

        $th = new Tp_transaction();
        $th->user = $user->id;
        $th->plan = $request->asset . " (" . $request->order_type . ")";
        $th->amount = $request->amount;
        $th->type = "Trading Investment";
        $th->save();

        // 5. Notify user
        $this->notificationService->createNotification(
            $user->id,
            "New {$request->order_type} trade opened on {$request->asset}.",
            "Trade Opened",
            'success',
            route('trade.monitor', $trade->id),
            'fa-chart-line',
            'bg-success/10'
        );

        // 6. Redirect to prevent MethodNotAllowed on refresh
        return redirect()->route('trade.monitor', $trade->id)->with('success', 'Trade placed successfully!');
    }
    
    /**
     * Cancel an existing plan
     */
    public function cancelPlan($plan)
    {
        $plan = User_plans::find($plan);
        $plan->active = 'cancelled';
        $plan->save();

        User::where('id', $plan->user)->increment('account_bal', $plan->amount);

        $th = new Tp_transaction();
        // CHANGED: 'assets' to 'plan' to match your database column
        $th->plan = $plan->plan; 
        $th->user = $plan->user;
        $th->amount = $plan->amount;
        $th->type = "Investment capital for cancelled plan";
        $th->save();

        return back()->with('success', 'Plan cancelled successfully');
    }
}