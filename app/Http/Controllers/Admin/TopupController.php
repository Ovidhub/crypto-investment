<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Tp_Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Models\Settings;

class TopupController extends Controller
{
   
   protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    //top up route
    public function topup(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $userdpo = Deposit::where('user', $request['user_id'])->first();
        $settings = Settings::where('id', '1')->first();

        $user_bal=$user->account_bal;
        $user_bonus=$user->bonus;
        $user_roi=$user->roi;
        $user_Ref=$user->ref_bonus;
        $user_deposit = $userdpo->amount;
  
        if($request['t_type']=="Credit") {
            if ($request['type']=="Bonus") {
                User::where('id', $request['user_id'])
                ->update([
                'bonus'=> $user_bonus + $request['amount'],
                'account_bal'=> $user_bal + $request->amount,
                ]);
                
                // Create notification for bonus credit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "You have received a bonus credit of {$formattedAmount} to your account.",
                    "Bonus Credit",
                    'success',
                    route('accounthistory'),
                    'fa-gift',
                    'bg-secondary/10'
                );
            }elseif ($request['type']=="Profit") {
                User::where('id', $request->user_id)
                ->update([
                    'roi'=> $user_roi + $request->amount,
                    'account_bal'=> $user_bal + $request->amount,
                ]);
                
                // Create notification for profit credit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "You have received a profit credit of {$formattedAmount} to your account.",
                    "Profit Credit",
                    'success',
                    route('accounthistory'),
                    'fa-arrow-up',
                    'bg-secondary/10'
                );
            }elseif($request['type']=="Ref_Bonus"){
                User::where('id', $request->user_id)
                ->update([
                    'ref_bonus'=> $user_Ref + $request->amount,
                    'account_bal'=> $user_bal + $request->amount,
                ]);
                
                // Create notification for referral bonus
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "You have received a referral bonus of {$formattedAmount} to your account.",
                    "Referral Bonus",
                    'success',
                    route('accounthistory'),
                    'fa-users',
                    'bg-secondary/10'
                );
            }elseif($request['type']=="balance"){
                User::where('id', $request->user_id)
                ->update([
                    'account_bal'=> $user_bal + $request->amount,
                ]);
                
                // Create notification for balance credit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "Your account has been credited with {$formattedAmount}.",
                    "Account Credit",
                    'success',
                    route('accounthistory'),
                    'fa-credit-card',
                    'bg-secondary/10'
                );
            }elseif ($request['type']=="Deposit") {
                $dp=new Deposit();
                $dp->amount= $request['amount'];
                $dp->payment_mode= 'Express Deposit';
                $dp->status= 'Processed';
                $dp->plan= $request['user_pln'];
                $dp->user= $request['user_id'];
                $dp->save();

                User::where('id', $request['user_id'])
                ->update([
                    'account_bal'=> $user_bal + $request->amount,
                ]);
                
                // Create notification for deposit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createDepositNotification(
                    $user, 
                    $formattedAmount, 
                    'fa-badge-check', 
                    route('deposits')
                );
            }
            
            //add history
            Tp_Transaction::create([
            'user' => $request->user_id,
            'plan' => "Credit",
            'amount'=>$request->amount,
            'type'=>$request->type,
            ]);
        
        }elseif($request['t_type']=="Debit") {
          if ($request['type']=="Bonus") {
            User::where('id', $request['user_id'])
              ->update([
                'bonus'=> $user_bonus - $request['amount'],
                'account_bal'=> $user_bal - $request->amount,
              ]);
              
              // Create notification for bonus debit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "A bonus amount of {$formattedAmount} has been deducted from your account.",
                    "Bonus Deduction",
                    'warning',
                    route('accounthistory'),
                    'fa-circle-minus',
                    'bg-warning/10'
                );
          }elseif ($request['type']=="Profit") {
              User::where('id', $request->user_id)
                ->update([
                  'roi'=> $user_roi - $request->amount,
                  'account_bal'=> $user_bal - $request->amount,
                ]);
                
              // Create notification for profit debit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "A profit amount of {$formattedAmount} has been deducted from your account.",
                    "Profit Deduction",
                    'warning',
                    route('accounthistory'),
                    'fa-arrow-down',
                    'bg-warning/10'
                );
            }elseif($request['type']=="Ref_Bonus"){
              User::where('id', $request->user_id)
                ->update([
                  'Ref_Bonus'=> $user_Ref - $request->amount,
                  'account_bal'=> $user_bal - $request->amount,
                ]);
                
              // Create notification for referral bonus debit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "A referral bonus amount of {$formattedAmount} has been deducted from your account.",
                    "Referral Bonus Deduction",
                    'warning',
                    route('accounthistory'),
                    'fa-users',
                    'bg-warning/10'
                );
            }
            elseif($request['type']=="balance"){
                User::where('id', $request->user_id)
                  ->update([
                    'account_bal'=> $user_bal - $request->amount,
                  ]);
                  
                // Create notification for balance debit
                $formattedAmount = $settings->currency . number_format($request->amount, 2);
                $this->notificationService->createNotification(
                    $user->id,
                    "An amount of {$formattedAmount} has been deducted from your account balance.",
                    "Account Debit",
                    'warning',
                    route('accounthistory'),
                    'fa-circle-minus',
                    'bg-warning/10'
                );
              }
            
             //add history
            Tp_Transaction::create([
                'user' => $request->user_id,
                'plan' => "Credit reversal",
                'amount'=>$request->amount,
                'type'=>$request->type,
            ]);
        
        }
        return redirect()->back()->with('success', 'Action Successful!');
    }
}