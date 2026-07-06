<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Wdmethod;
use App\Models\Withdrawal;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class ManageWithdrawalController extends Controller
{
    
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    //process withdrawals
    public function pwithdrawal(Request $request)
    {
        $withdrawal = Withdrawal::where('id', $request->id)->first();
        $user = User::where('id', $withdrawal->user)->first();
        $settings = Settings::where('id', '=', '1')->first();

        // Normalise the requested action. We support three outcomes:
        //   Complete  -> funds received into the user's address  (stored 'Processed')
        //   Pending   -> request is held / awaiting              (stored 'Pending')
        //   Decline   -> request rejected, balance refunded      (stored 'Declined')
        // "Paid" is kept as an alias of Complete for backward compatibility.
        $action = $request->action;
        if ($action === 'Paid') {
            $action = 'Complete';
        }

        $formattedAmount = $settings->currency . number_format($withdrawal->amount, 2);

        // States in which the user's balance is currently debited (funds held/sent).
        $heldStates = ['Pending', 'Processed'];
        $wasHeld = in_array($withdrawal->status, $heldStates);

        if ($action === 'Decline') {
            // Refund the held amount only once (i.e. when it was actually held).
            if ($wasHeld && $withdrawal->user == $user->id) {
                User::where('id', $user->id)->update([
                    'account_bal' => $user->account_bal + $withdrawal->to_deduct,
                ]);
            }

            Withdrawal::where('id', $request->id)->update(['status' => 'Declined']);

            $message = "Your withdrawal request of {$formattedAmount} was declined.";
            if ($request->reason) {
                $message .= " Reason: {$request->reason}";
            }
            $this->notificationService->createNotification(
                $user->id,
                $message,
                "Withdrawal Declined",
                'warning',
                route('withdrawalsdeposits'),
                'fa-circle-x',
                'bg-warning/10'
            );

            if ($request->emailsend == "true") {
                try {
                    Mail::to($user->email)->send(new NewNotification($request->reason, $request->subject, $user->name));
                } catch (\Exception $e) {
                    \Log::error('Failed to send withdrawal decline email: ' . $e->getMessage());
                }
            }

            return redirect()->route('mwithdrawals')->with('success', 'Withdrawal declined and balance refunded.');
        }

        // Complete or Pending. If it was previously Declined the balance was
        // refunded, so re-hold (debit) it before moving back to a held state.
        if ($withdrawal->status === 'Declined') {
            if ($user->account_bal < $withdrawal->to_deduct) {
                return redirect()->back()
                    ->with('message', 'User balance is insufficient to re-hold this withdrawal. Ask the user to fund their account or keep it declined.');
            }
            User::where('id', $user->id)->update([
                'account_bal' => $user->account_bal - $withdrawal->to_deduct,
            ]);
        }

        if ($action === 'Pending') {
            Withdrawal::where('id', $request->id)->update(['status' => 'Pending']);

            $this->notificationService->createNotification(
                $user->id,
                "Your withdrawal request of {$formattedAmount} is pending and awaiting processing.",
                "Withdrawal Pending",
                'info',
                route('withdrawalsdeposits'),
                'fa-clock',
                'bg-primary/10'
            );

            return redirect()->route('mwithdrawals')->with('success', 'Withdrawal set to pending.');
        }

        // Complete -> mark as received (stored as 'Processed').
        Withdrawal::where('id', $request->id)->update(['status' => 'Processed']);

        $message = "This is to inform you that your withdrawal request of {$formattedAmount} has been completed and the funds have been received into your address.";
        try {
            Mail::to($user->email)->send(new NewNotification($message, 'Withdrawal Received', $user->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send withdrawal completion email: ' . $e->getMessage());
        }

        $this->notificationService->createWithdrawalNotification(
            $user,
            $formattedAmount,
            'fa-thumbs-up',
            route('withdrawalsdeposits')
        );

        return redirect()->route('mwithdrawals')->with('success', 'Withdrawal completed — marked as received.');
    }

    
    public function processwithdraw($id){
         $with = Withdrawal::where('id',$id)->first();
         $method = Wdmethod::where('name', $with->payment_mode)->first();
         $user = User::where('id', $with->user)->first();
        return view('admin.Withdrawals.pwithrdawal',[
            'withdrawal' => $with,
            'method' => $method,
            'user' => $user,
            'title'=>'Process withdrawal Request',
        ]);
    }
}