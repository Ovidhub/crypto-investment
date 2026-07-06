<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Settings;
use App\Models\Plans;
use App\Models\User_plans;
use App\Models\Tp_Transaction;
use App\Mail\NewRoi;
use App\Mail\endplan;
use App\Mail\NewNotification;
use App\Models\Mt4Details;
use App\Models\ExpertTrader;
use App\Models\ExpertTraderSubscriber;
use App\Traits\BinanceApi;
use App\Traits\Coinpayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NotificationService;

class AutoTaskController extends Controller
{
    use Coinpayment, BinanceApi;
      protected $notificationService;
    
    public function __construct()
    {
        $this->notificationService = app(NotificationService::class);
    }
    /*
        Automatic toup
        calculate top up earnings and
        auto increment earnings after the increment time
    */

    public function autotopup()
    {
        // automatic roi
        $this->automaticRoi();

        // check for subscription expiration
      
    }



    public function checkSubscription()
    {
        $subscriptions = Mt4Details::where('status', 'active')->get();
        $today = now();
        $settings = Settings::find(1);

        foreach ($subscriptions as $sub) {
            $endAt = Carbon::parse($sub->end_date);
            $remindAt = Carbon::parse($sub->reminded_at);
            $singleSub = Mt4Details::find($sub->id);
            $user = User::find($singleSub->client_id);

            if ($today->isSameDay($endAt) && $singleSub->status != 'Expired') {
                //mark sub as expired
                $singleSub->status = 'Expired';
                $singleSub->save();

                //send email to user
                try {
                    $messageUser = "Your subscription with MT4-ID: $sub->mt4_id have expired. To enable us continue trading on this account, please renew your subcription. \r\n To renew your subcription, login to your $settings->site_name account, go to managed accounts page and click on the renew button on the affected account.";
                    Mail::to($user->email)->send(new NewNotification($messageUser, 'Your subscription have expired', $user->firstname));
                } catch (\Exception $e) {
                    \Log::error('Failed to send subscription expiration email to user: ' . $e->getMessage());
                }

                // Send email to admin
                try {
                    $messageAdmin = "Subscription with MT4-ID: $sub->mt4_id have expired and the user have been notified.";
                    Mail::to($settings->contact_email)->send(new NewNotification($messageAdmin, 'Your subscription have expired', 'Admin'));
                } catch (\Exception $e) {
                    \Log::error('Failed to send subscription expiration email to admin: ' . $e->getMessage());
                }
            }

            if ($today->isSameDay($remindAt)) {
                // number of days for subscription to expire
                $daysLeft = $endAt->diffInDays($remindAt);

                //send email to user
                try {
                    $message = "Your subscription with MT4-ID: $sub->mt4_id will expire in $daysLeft days. To avoid disconnection of your trading account, please renew your subcription before $endAt. \r\n To renew your subcription, login to your $settings->site_name account, go to managed accounts page and click on the renew button on the affected account.";
                    Mail::to($singleSub->tuser->email)->send(new NewNotification($message, 'Your subscription will expire soon', $singleSub->tuser->firstname));
                } catch (\Exception $e) {
                    \Log::error('Failed to send subscription reminder email: ' . $e->getMessage());
                }

                $singleSub->reminded_at = $remindAt->addDay();   //2022-12-21 19:50:58
                $singleSub->save();
            }
        }
    }




 public function automaticRoi()
{
    $settings = Settings::find(1);

    if ($settings->trade_mode == 'on') {
        $usersPlans = User_plans::where('active', 'yes')->get();
        $now = now();

        foreach ($usersPlans as $plan) {
            $dplan = Plans::find($plan->plan);
            $user = User::find($plan->user);

            // Determine increment interval in minutes
            switch ($dplan->increment_interval) {
                case "Monthly":
                    $intervalMinutes = 30 * 24 * 60; // 30 days in minutes
                    break;
                case "Weekly":
                    $intervalMinutes = 7 * 24 * 60; // 7 days in minutes
                    break;
                case "Daily":
                    $intervalMinutes = 24 * 60; // 24 hours in minutes
                    break;
                case "Hourly":
                    $intervalMinutes = 60; // 60 minutes
                    break;
                case "Every 30 Minutes":
                    $intervalMinutes = 30;
                    break;
                default:
                    $intervalMinutes = 5; // Default 5 minutes
                    break;
            }

            $isActive = $now->lessThanOrEqualTo($plan->expire_date) && $user->trade_mode === 'on';
            $isExpired = $now->greaterThan($plan->expire_date);

            // Calculate ROI increment
            $increment = $dplan->increment_type === "Percentage"
                ? ($plan->amount * $dplan->increment_amount) / 100
                : $dplan->increment_amount;

            // Process ROI if active and within window
            if ($isActive) {
                $canTradeNow = $now->isWeekday() || $settings->weekend_trade === 'on';
                
                // Calculate minutes since last growth
                $minutesSinceLastGrowth = $plan->last_growth->diffInMinutes($now);
                $shouldPayRoi = $minutesSinceLastGrowth >= $intervalMinutes;

                if ($canTradeNow && $shouldPayRoi) {
                    User::where('id', $plan->user)->update([
                        'roi' => $user->roi + $increment,
                        'account_bal' => $user->account_bal + $increment,
                    ]);

                    Tp_Transaction::create([
                        'plan' => $dplan->name,
                        'user' => $user->id,
                        'amount' => $increment,
                        'user_plan_id' => $plan->id,
                        'type' => 'ROI',
                    ]);
                    
                     // Create notification for ROI earnings
                            $formattedAmount = $settings->currency . number_format($increment, 2);
                            $this->notificationService->createRoiNotification(
                                $user, 
                                $dplan->name, 
                                $formattedAmount, 
                                route('accounthistory')
                            );

                    User_plans::where('id', $plan->id)->update([
                        'last_growth' => $now,
                        'profit_earned' => $plan->profit_earned + $increment,
                    ]);

                    // if ($user->sendroiemail === 'Yes') {
                    //     Mail::to($user->email)->send(new NewRoi(
                    //         $user,
                    //         $dplan->name,
                    //         $increment,
                    //         $now->toDateTimeString(),
                    //         'New Return on Investment(ROI)'
                    //     ));
                    // }
                }

                // If trading not allowed on weekends but interval passed, update last_growth
                if ($now->isWeekend() && $settings->weekend_trade !== 'on' && $shouldPayRoi) {
                    User_plans::where('id', $plan->id)->update([
                        'last_growth' => $now,
                    ]);
                }
            }

            // Handle expired plans
            if ($isExpired) {
                if ($settings->return_capital) {
                    User::where('id', $plan->user)->update([
                        'account_bal' => $user->account_bal + $plan->amount,
                    ]);

                    Tp_Transaction::create([
                        'plan' => $dplan->name,
                        'user' => $plan->user,
                        'amount' => $plan->amount,
                        'type' => 'Investment capital',
                    ]);
                    
                    
                     // Create notification for capital return
                        $formattedAmount = $settings->currency . number_format($plan->amount, 2);
                        $this->notificationService->createNotification(
                            $user->id,
                            "Your investment capital of {$formattedAmount} from {$dplan->name} plan has been returned to your account balance.",
                            "Investment Capital Returned",
                            'info',
                            route('accounthistory'),
                            'fa-arrows-rotate',
                            'bg-primary/10'
                        );
                }

                User_plans::where('id', $plan->id)->update([
                    'active' => 'expired',
                ]);
                
                
                // Create notification for plan expiration
                    $this->notificationService->createNotification(
                        $user->id,
                        "Your investment in {$dplan->name} plan has expired. Any earnings have been credited to your account.",
                        "Investment Plan Expired",
                        'info',
                        route('myplans', ['sort' => 'expired']),
                        'alert-circle',
                        'bg-primary/10'
                    );

                if ($user->sendinvplanemail === 'Yes') {
                    try {
                        $objDemo = new \stdClass();
                        $objDemo->receiver_email = $user->email;
                        $objDemo->receiver_plan = $dplan->name;
                        $objDemo->received_amount = "{$settings->currency}{$plan->amount}";
                        $objDemo->sender = $settings->site_name;
                        $objDemo->receiver_name = $user->name;
                        $objDemo->date = $now;
                        $objDemo->subject = "Investment plan closed";

                        Mail::to($user->email)->send(new endplan($objDemo));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send investment plan closed email: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}

    /**
     * Automatic Copy Trading Profit Distribution
     * Process automatic profit distribution for copy trading subscribers
     */
    public function automaticCopyTradingProfit()
    {
        $settings = Settings::find(1);

        // Check global toggle
        if ($settings->copy_trading_auto_distribute !== 'on') {
            \Log::info('Copy trading auto distribution is disabled');
            return;
        }

        // Check trade mode
        if ($settings->trade_mode !== 'on') {
            \Log::info('Trade mode is off, skipping copy trading distribution');
            return;
        }

        // Get active traders with auto_distribute enabled
        $traders = ExpertTrader::where('status', 'active')
            ->where('auto_distribute', 'on')
            ->get();

        $now = now();
        $processedCount = 0;
        $totalDistributed = 0;

        foreach ($traders as $trader) {
            try {
                // Determine interval in minutes
                $intervalMinutes = $this->getIntervalMinutes($trader->distribution_interval);

                // Get active subscribers
                $subscribers = ExpertTraderSubscriber::where('expert_trader_id', $trader->id)
                    ->where('status', 'active')
                    ->where('end_date', '>=', $now)
                    ->get();

                if ($subscribers->isEmpty()) {
                    continue;
                }

                // Check weekend trading
                $canTradeNow = $now->isWeekday() || $settings->weekend_trade === 'on';
                if (!$canTradeNow) {
                    \Log::info("Skipping trader {$trader->id} due to weekend trading restrictions");
                    continue;
                }

                foreach ($subscribers as $subscriber) {
                    // Calculate minutes since last profit
                    $minutesSinceLastProfit = $subscriber->last_profit_added 
                        ? $subscriber->last_profit_added->diffInMinutes($now)
                        : PHP_INT_MAX; // Never distributed = eligible

                    $shouldDistribute = $minutesSinceLastProfit >= $intervalMinutes;

                    if ($shouldDistribute) {
                        DB::beginTransaction();
                        
                        try {
                            $user = User::find($subscriber->user_id);
                            
                            if (!$user) {
                                \Log::warning("User {$subscriber->user_id} not found for subscriber {$subscriber->id}");
                                continue;
                            }

                            // Calculate profit
                            $profit = ($subscriber->amount_invested * $trader->distribution_percentage) / 100;

                            // Update subscriber
                            $subscriber->current_profit += $profit;
                            $subscriber->last_profit_added = $now;
                            $subscriber->save();

                            // Update user balance and ROI
                            $user->account_bal += $profit;
                            $user->roi += $profit;
                            $user->save();

                            // Create transaction record
                            Tp_Transaction::create([
                                'user' => $user->id,
                                'plan' => "Copy Trading Profit: {$trader->name}",
                                'amount' => $profit,
                                'type' => 'Copy Trading Profit',
                                'status' => 'Processed',
                            ]);

                            // Update trader total profit
                            $trader->total_profit += $profit;
                            $trader->save();

                            // Create notification
                            $formattedAmount = $settings->currency . number_format($profit, 2);
                            $this->notificationService->createNotification(
                                $user->id,
                                "You received {$formattedAmount} profit from copy trading {$trader->name}.",
                                "Copy Trading Profit Received",
                                'success',
                                route('copytrading.subscriptions'),
                                'fa-chart-line',
                                'bg-success/10'
                            );

                            DB::commit();
                            
                            $processedCount++;
                            $totalDistributed += $profit;

                            \Log::info("Distributed {$profit} to user {$user->id} from trader {$trader->name}");

                        } catch (\Exception $e) {
                            DB::rollBack();
                            \Log::error("Failed to distribute profit for subscriber {$subscriber->id}: " . $e->getMessage());
                            continue;
                        }
                    }
                }

            } catch (\Exception $e) {
                \Log::error("Error processing trader {$trader->id}: " . $e->getMessage());
                continue;
            }
        }

        \Log::info("Automatic copy trading distribution completed: {$processedCount} distributions, total: {$totalDistributed}");
    }

    /**
     * Convert distribution interval to minutes
     *
     * @param string $interval
     * @return int
     */
    private function getIntervalMinutes($interval)
    {
        switch ($interval) {
            case "5 Minutes":
                return 5;
            case "30 Minutes":
                return 30;
            case "Hourly":
                return 60;
            case "Daily":
                return 24 * 60;
            case "Weekly":
                return 7 * 24 * 60;
            case "Monthly":
                return 30 * 24 * 60;
            default:
                return 24 * 60; // Default to daily
        }
    }
}
