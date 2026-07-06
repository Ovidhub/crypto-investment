<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\User_plans;
use App\Models\Wdmethod;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\WithdrawalStatus;
use App\Traits\Coinpayment;
use App\Services\NotificationService;

class WithdrawalController extends Controller
{
    use Coinpayment;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    //
    public function withdrawamount(Request $request)
    {
        $request->session()->put('paymentmethod', $request->method);
        return redirect()->route('withdrawfunds');
    }

    //Return withdrawals route
    public function withdrawfunds()
    {
        $paymethod = session('paymentmethod');
        $checkmethod =  Wdmethod::where('name', $paymethod)->first();
        if ($checkmethod->defaultpay == "yes") {
            $default = true;
        } else {
            $default = false;
        }

        if ($checkmethod->methodtype == "crypto") {
            $methodtype = 'crypto';
        } else {
            $methodtype = 'currency';
        }

        // Get fee percentage and min/max withdrawal limits
        $fee_percentage = $checkmethod->charges_type == 'percentage' ? $checkmethod->charges_amount : 0;
        $min_withdrawal = $checkmethod->minimum;
        $max_withdrawal = $checkmethod->maximum ?? null;

        return view('user.withdraw', [
            'title' => 'Complete Withdrawal Request',
            'payment_mode' => $paymethod,
            'default' => $default,
            'methodtype' => $methodtype,
            'fee_percentage' => $fee_percentage,
            'min_withdrawal' => $min_withdrawal,
            'max_withdrawal' => $max_withdrawal,
        ]);
    }

    public function getotp()
    {
        $settings = Settings::where('id', '1')->first();
        $code = $this->RandomStringGenerator(8);

        $user = Auth::user();
        User::where('id', $user->id)->update([
            'withdrawotp' => $code,
        ]);

        // $message = "You have initiated a withdrawal request, use the OTP: $code to complete your request.";
        $subject = "OTP Request";
        // Mail::bcc($user->email)->send(new NewNotification($message, $subject, $user->name));

        // Create notification for OTP request
        $this->notificationService->createNotification(
            $user->id,
            "A withdrawal code has been generated. Please contact support via live chat or email us at  $settings->contact_email for further assistance.",
            "Withdrawal Code",
            'info',
            null,
            'fa-shield',
            'bg-primary/10'
        );

        return redirect()->back()
            ->with('success', "A withdrawal code has been generated. Please contact support via live chat or email us at $settings->contact_email  for further assistance.");
    }

    public function completewithdrawal(Request $request)
    {

        $settings = Settings::where('id', '1')->first();
        if (Auth::user()->sendotpemail == "Yes") {
            if ($request->otpcode != Auth::user()->withdrawotp) {
                return redirect()->back()->with('message', "Withdrawal Code is incorrect,Please contact support via live chat or email us at $settings->contact_email");
            }
        }


        if ($settings->enable_kyc == "yes") {
            if (Auth::user()->account_verify != "Verified") {
                return redirect()->back()->with('message', 'Your account must be verified before you can make withdrawal.');
            }
        }

        $method = Wdmethod::where('name', $request->method)->first();
        if ($method->charges_type == 'percentage') {
            $charges = $request['amount'] * $method->charges_amount / 100;
        } else {
            $charges = $method->charges_amount;
        }

        $to_withdraw = $request['amount'] + $charges;
        //return if amount is lesser than method minimum withdrawal amount

        if (Auth::user()->account_bal < $to_withdraw) {
            return redirect()->back()
                ->with('message', 'Sorry, your account balance is insufficient for this request.');
        }

        if ($request['amount'] < $method->minimum) {
            return redirect()->back()
                ->with("message", "Sorry, The minimum amount you can withdraw is $settings->currency$method->minimum, please try another payment method.");
        }

        //get user last investment package
        User_plans::where('user', Auth::user()->id)
            ->where('active', 'yes')
            ->orderBy('activated_at', 'asc')->first();

        //get user
        $user = User::where('id', Auth::user()->id)->first();

        if ($request->method == 'Bitcoin') {
            if (empty($user->btc_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Bitcoin Wallet Address');
            }
            $coin = "BTC";
            $wallet = $user->btc_address;
        } elseif ($request->method  == 'Ethereum') {
            if (empty($user->eth_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Ethereum Wallet Address');
            }
            $coin = "ETH";
            $wallet = $user->eth_address;
        } elseif ($request->method  == 'Litecoin') {
            if (empty($user->ltc_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Litecoin Wallet Address');
            }
            $coin = "LTC";
            $wallet = $user->ltc_address;
        } elseif ($request->method  == 'USDT') {
            if (empty($user->usdt_address)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your USDT Wallet Address');
            }
            $coin = "USDT.TRC20";
            $wallet = $user->usdt_address;
        } elseif ($request->method  == 'Bank Transfer') {
            if (empty($user->account_name) or empty($user->bank_name) or empty($user->account_number)) {
                return redirect()->route('profile')
                    ->with('message', 'Please Setup your Bank Account Details');
            }
        }

        $amount = $request['amount'];
        $ui = $user->id;


   if ($settings->withdrawal_option == "auto" and ($request->method == 'Bitcoin' or $request->method  == 'Litecoin' or $request->method  == 'Ethereum' or $request->method == 'USDT')) {
            return $this->cpwithdraw($amount, $coin, $wallet, $ui, $to_withdraw);
        }

            //debit user
            User::where('id', $user->id)->update([
                'account_bal' => $user->account_bal - $to_withdraw,
                'withdrawotp' => NULL,
            ]);




        //save withdrawal info
        $dp = new Withdrawal();
        $dp->amount = $amount;
        $dp->to_deduct = $to_withdraw;
        $dp->payment_mode = $request->method;
        $dp->status = 'Pending';
        $dp->paydetails = $request->details;
        $dp->user = $user->id;
        $dp->save();

        // send mail to admin
        try {
            Mail::to($settings->contact_email)->send(new WithdrawalStatus($dp, $user, 'Withdrawal Request', true));
        } catch (\Exception $e) {
            \Log::error('Failed to send withdrawal notification email to admin: ' . $e->getMessage());
        }

        //send notification to user
        try {
            Mail::to($user->email)->send(new WithdrawalStatus($dp, $user, 'Successful Withdrawal Request'));
        } catch (\Exception $e) {
            \Log::error('Failed to send withdrawal confirmation email to user: ' . $e->getMessage());
        }

        // Create in-app notification
        $formattedAmount = $settings->currency . number_format($amount, 2);
        $this->notificationService->createWithdrawalNotification(
            $user,
            $formattedAmount,
            'fa-clock',
            route('withdrawalsdeposits')
        );

        return redirect()->route('withdrawalsdeposits')
            ->with('success', 'Action Sucessful! Please wait while we process your request.');
    }


    // for front end content management
    function RandomStringGenerator($n)
    {
        $generated_string = "";
        $domain = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $len = strlen($domain);
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, $len - 1);
            $generated_string = $generated_string . $domain[$index];
        }
        // Return the random generated string
        return $generated_string;
    }
}
