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
        $withdrawal=Withdrawal::where('id',$request->id)->first();
        $user=User::where('id',$withdrawal->user)->first();
        $settings=Settings::where('id', '=', '1')->first();

        if ($request->action == "Paid") {
            Withdrawal::where('id',$request->id)
            ->update([
                'status' => 'Processed',
            ]);

            $message = "This is to inform you that your withdrawal request of $settings->currency$withdrawal->amount have approved and funds have been sent to your selected account";

            try {
                Mail::to($user->email)->send(new NewNotification($message, 'Successful Withdrawal', $user->name));
            } catch (\Exception $e) {
                \Log::error('Failed to send withdrawal approval email: ' . $e->getMessage());
            }
            
            // Create notification for processed withdrawal
            $formattedAmount = $settings->currency . number_format($withdrawal->amount, 2);
            $this->notificationService->createWithdrawalNotification(
                $user, 
                $formattedAmount, 
                'fa-thumbs-up', 
                route('withdrawalsdeposits')
            );
        } else {
            if($withdrawal->user==$user->id){
                User::where('id',$user->id)
                ->update([
                    'account_bal' => $user->account_bal+$withdrawal->to_deduct,
                ]); 
                Withdrawal::where('id',$request->id)->delete();

                // Create notification for rejected withdrawal
                $formattedAmount = $settings->currency . number_format($withdrawal->amount, 2);
                $message = "Your withdrawal request of {$formattedAmount} was not processed.";
                if ($request->reason) {
                    $message .= " Reason: {$request->reason}";
                }
                $this->notificationService->createNotification(
                    $user->id,
                    $message,
                    "Withdrawal Cancelled",
                    'warning',
                    route('withdrawalsdeposits'),
                    'fa-circle-x',
                    'bg-warning/10'
                );

                if ($request->emailsend == "true") {
                    try {
                        Mail::to($user->email)->send(new NewNotification($request->reason,$request->subject, $user->name));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send withdrawal rejection email: ' . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->route('mwithdrawals')->with('success', 'Action Sucessful!');
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