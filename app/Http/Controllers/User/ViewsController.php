<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CryptoAccount;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Plans;
use App\Models\User_plans;
use App\Models\Mt4Details;
use App\Models\Deposit;
use App\Models\SettingsCont;
use App\Models\Wdmethod;
use App\Models\Withdrawal;
use App\Models\Tp_Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ViewsController extends Controller
{

    public function dashboard(Request $request)
    {

        $settings = Settings::where('id', '1')->first();
        $user = User::find(auth()->user()->id);

        //check if user does not have ref link then update his link
        if ($user->ref_link == '') {
            User::where('id', $user->id)
                ->update([
                    'ref_link' => $settings->site_address . '/ref/' . $user->username,
                ]);
        }

        //give reg bonus if new
        if ($user->signup_bonus != "received" && ($settings->signup_bonus != NULL && $settings->signup_bonus > 0)) {
            User::where('id', $user->id)
                ->update([
                    'bonus' => $user->bonus + $settings->signup_bonus,
                    'account_bal' => $user->account_bal + $settings->signup_bonus,
                    'signup_bonus' => "received",
                ]);
            //create history
            Tp_Transaction::create([
                'user' => Auth::user()->id,
                'plan' => "SignUp Bonus",
                'amount' => $settings->signup_bonus,
                'type' => "Bonus",
            ]);
        }

        if (DB::table('crypto_accounts')->where('user_id', Auth::user()->id)->doesntExist()) {
            $cryptoaccnt = new CryptoAccount();
            $cryptoaccnt->user_id = Auth::user()->id;
            $cryptoaccnt->save();
        }

        //sum total deposited
        $total_deposited = DB::table('deposits')->where('user', $user->id)->where('status', 'Processed')->sum('amount');

        $total_withdrawal = DB::table('withdrawals')->where('user', $user->id)->where('status', 'Processed')->sum('amount');

        //log user out if not blocked by admin
        if ($user->status != "active") {
            $request->session()->flush();
            return redirect()->route('dashboard');
        }

        // Get featured expert traders (top 3 by ROI with automation enabled)
        $featuredTraders = \App\Models\ExpertTrader::where('status', 'active')
            ->where('auto_distribute', 'on')
            ->orderByDesc('roi_percentage')
            ->take(3)
            ->get();

        // Get user's active copy trading subscriptions
        $userSubscriptions = \App\Models\ExpertTraderSubscriber::where('user_id', Auth::id())
            ->where('status', 'active')
            ->pluck('expert_trader_id')
            ->toArray();

        // Calculate profit growth percentage (compare last 7 days vs previous 7 days)
        $currentPeriodStart = now()->subDays(7);
        $previousPeriodStart = now()->subDays(14);
        $previousPeriodEnd = now()->subDays(7);

        $currentPeriodProfit = Tp_Transaction::where('user', $user->id)
            ->whereIn('type', ['ROI', 'Profit', 'Bonus', 'Referral Bonus'])
            ->where('created_at', '>=', $currentPeriodStart)
            ->sum('amount');

        $previousPeriodProfit = Tp_Transaction::where('user', $user->id)
            ->whereIn('type', ['ROI', 'Profit', 'Bonus', 'Referral Bonus'])
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->sum('amount');

        // Calculate percentage change
        if ($previousPeriodProfit > 0) {
            $profitGrowth = (($currentPeriodProfit - $previousPeriodProfit) / $previousPeriodProfit) * 100;
        } elseif ($currentPeriodProfit > 0) {
            $profitGrowth = 100; // 100% growth if no previous profit but current profit exists
        } else {
            $profitGrowth = 0;
        }

        return view('user.dashboard', [
            'title' => 'Account Dashboard',
            'deposited' => $total_deposited,
            'total_withdrawal' => $total_withdrawal,
            'trading_accounts' => Mt4Details::where('client_id', Auth::user()->id)->count(),
            'plans' => User_plans::where('user', Auth::user()->id)->where('active', 'yes')->orderByDesc('id')->skip(0)->take(2)->get(),
            'featuredTraders' => $featuredTraders,
            'userSubscriptions' => $userSubscriptions,
            'profitGrowth' => $profitGrowth,
            't_history' => Tp_Transaction::where('user', Auth::user()->id)
                ->where('type', '<>', 'ROI')
                ->orderByDesc('id')->skip(0)->take(10)
                ->get(),
        ]);
    }

    //Profile route
    public function profile()
    {
        $userinfo = User::where('id', Auth::user()->id)->first();
        return view('user.profile')->with(array(
            'userinfo' => $userinfo,
            'title' => 'Profile',
        ));
    }

    //return add withdrawal account form view
    public function accountdetails()
    {
        return view('user.updateacct')->with(array(
            'title' => 'Update account details',
        ));
    }


    //support route
    public function support()
    {
        return view('user.support')
            ->with(array(
                'title' => 'Support',
            ));
    }

    //Trading history route
    public function tradinghistory()
    {
        return view('user.thistory')
            ->with(array(
                't_history' => Tp_Transaction::where('user', Auth::user()->id)
                    ->whereIn('type', ['ROI', 'Profit', 'Copy Trading Profit', 'Crypto Staking Reward','Copy Trading Return']) 
                    ->orderByDesc('id')
                    ->paginate(15),
                'title' => 'Trading History',
            ));
    }

    //Account transactions history route
    public function accounthistory()
    {
        return view('user.transactions')
            ->with(array(
                't_history' => Tp_Transaction::where('user', Auth::user()->id)
                    ->where('type', '<>', ['ROI', 'Profit', 'Copy Trading Profit', 'Crypto Staking Reward','Copy Trading Return'])
                    ->orderByDesc('id')
                    ->paginate(15, ['*'], 'other_page'),

                'withdrawals' => Withdrawal::where('user', Auth::user()->id)->orderBy('id', 'desc')
                    ->paginate(15, ['*'], 'withdrawal_page'),
                'deposits' => Deposit::where('user', Auth::user()->id)->orderBy('id', 'desc')
                    ->paginate(15, ['*'], 'deposit_page'),
                'title' => 'Account Transactions History',

            ));
    }

    //Return deposit route
    public function deposits()
    {
        $paymethod = Wdmethod::where(function ($query) {
            $query->where('type', '=', 'deposit')
                ->orWhere('type', '=', 'both');
        })->where('status', 'enabled')->orderByDesc('id')->get();

        //sum total deposited
        $total_deposited = DB::table('deposits')->where('user', auth()->user()->id)->where('status', 'Processed')->sum('amount');

        return view('user.deposits')
            ->with(array(
                'title' => 'Fund your account',
                'dmethods' => $paymethod,
                'deposits' => Deposit::where(['user' => Auth::user()->id])
                    ->orderBy('id', 'desc')
                    ->get(),
                'deposited' => $total_deposited,
            ));
    }

    //Return withdrawals route
    public function withdrawals()
    {
        $withdrawals =  Wdmethod::where(function ($query) {
            $query->where('type', '=', 'withdrawal')
                ->orWhere('type', '=', 'both');
        })->where('status', 'enabled')->orderByDesc('id')->get();

        return view('user.withdrawals')
            ->with(array(
                'title' => 'Withdraw Your funds',
                'wmethods' => $withdrawals,
            ));
    }

    public function transferview()
    {
        $settings = SettingsCont::find(1);
        if (!$settings->use_transfer) {
            abort(404);
        }
        return view('user.transfer', [
            'title' => 'Send funds to a friend',
        ]);
    }

    //Subscription Trading 
    public function subtrade()
    {
        $settings = Settings::where('id', 1)->first();
        $mod = $settings->modules;
        if (!$mod['subscription']) {
            abort(404);
        }
        return view('user.subtrade')
            ->with(array(
                'title' => 'Subscription Trade',
                'subscriptions' => Mt4Details::where('client_id', auth::user()->id)->orderBy('id', 'desc')->get(),
            ));
    }


    //Main Plans route
    public function mplans()
    {
        return view('user.mplans')
            ->with(array(
                'title' => 'Main Plans',
                'plans' => Plans::where('type', 'main')->get(),
                'settings' => Settings::where('id', '1')->first(),
            ));
    }

    //Stock Plans route
    public function stockPlans()
    {
        return view('user.stockplans')
            ->with(array(
                'title' => 'Stock Investment Plans',
                'settings' => Settings::where('id', '1')->first(),
            ));
    }

  //My Plans route
    public function myplans($sort)
    {
        $userId = Auth::user()->id;

        if ($sort == 'All') {
            return view('user.myplans')
                ->with(array(
                    'numOfPlan' => User_plans::where('user', $userId)->count(),
                    'title' => 'Your packages',
                    // Added ->with('dplan') here
                    'plans' => User_plans::with('dplan')->where('user', $userId)->orderByDesc('id')->paginate(10),
                    'settings' => Settings::where('id', '1')->first(),
                ));
        } else {
            return view('user.myplans')
                ->with(array(
                    'numOfPlan' => User_plans::where('user', $userId)->count(),
                    'title' => 'Your packages',
                    // Added ->with('dplan') here
                    'plans' => User_plans::with('dplan')->where('user', $userId)->where('active', $sort)->orderByDesc('id')->paginate(10),
                    'settings' => Settings::where('id', '1')->first(),
                ));
        }
    }


    public function sortPlans($sort)
    {
        return redirect()->route('myplans', ['sort' => $sort]);
    }

    public function planDetails($id)
    {
        $plan = User_plans::find($id);
        return view('user.plandetails', [
            'title' => $plan->dplan->name,
            'plan' => $plan,
            'transactions' => Tp_Transaction::where('type', 'ROI')->where('user_plan_id', $plan->id)->orderByDesc('id')->paginate(10),
        ]);
    }


    function twofa()
    {
        return view('profile.show', [
            'title' => 'Advance Security Settings',
        ]);
    }

    // Referral Page
    public function referuser()
    {
        return view('user.referuser', [
            'title' => 'Refer user',
        ]);
    }

    public function verifyaccount()
    {
        if (Auth::user()->account_verify == 'Verified') {
            abort(404, 'You do not have permission to access this page');
        }
        return view('user.verify', [
            'title' => 'Verify your Account',
        ]);
    }

    public function verificationForm()
    {
        if (Auth::user()->account_verify == 'Verified') {
            abort(404, 'You do not have permission to access this page');
        }
        return view('user.verification', [
            'title' => 'KYC Application'
        ]);
    }



    public function tradeSignals()
    {
        $settings = Settings::where('id', 1)->first();
        $mod = $settings->modules;
        if (!$mod['signal']) {
            abort(404);
        }

        $response = $this->fetctApi('/subscription', [
            'id' => auth()->user()->id
        ]);
        $res = json_decode($response);

        $responseSt = $this->fetctApi('/signal-settings');
        $info = json_decode($responseSt);

        return view('user.signals.subscribe', [
            'title' => 'Trade signals',
            'subscription' => $res->data,
            'set' => $info->data->settings,
        ]);
    }


    public function binanceSuccess()
    {
        return redirect()->route('deposits')->with('success', 'Your Deposit was successful, please wait while it is confirmed. You will receive a notification regarding the status of your deposit.');
    }

    public function binanceError()
    {
        return redirect()->route('deposits')->with('message', 'Something went wrong please try again. Contact our support center if problem persist');
    }
}