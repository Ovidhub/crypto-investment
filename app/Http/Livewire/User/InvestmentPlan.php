<?php

namespace App\Http\Livewire\User;

use App\Mail\NewNotification;
use App\Models\Plans;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use App\Services\NotificationService;

class InvestmentPlan extends Component
{
    public Plans $planSelected;
    public $amountToInvest = 0;
    public $disabled = 'disabled';
    public $paymentMethod;
    public $feedback = '';

    public function mount()
    {
        $this->paymentMethod = 'Account Balance';
        $lastPlan = Plans::where('plan_type', 'investment')->orderByDesc('id')->first();
        if ($lastPlan) {
            $this->planSelected = $lastPlan;
        }
    }

    public function render()
    {
        return view('livewire.user.investment-plan', [
            'plans' => Plans::where('plan_type', 'investment')->orderByDesc('id')->get(),
        ]);
    }

    public function selectPlan($id)
    {
        $this->planSelected = Plans::find($id);
        if ($this->paymentMethod and $this->amountToInvest and $this->planSelected) {
            $this->disabled = '';
        } else {
            $this->disabled = 'disabled';
        }
    }

    public function chanegePaymentMethod($method)
    {

        $this->paymentMethod = $method;

        if ($this->amountToInvest and $this->planSelected and $this->paymentMethod) {
            $this->disabled = '';
        } else {
            $this->disabled = 'disabled';
        }
    }

    public function selectAmount($value)
    {
        $this->amountToInvest = intval($value);

        if ($this->paymentMethod and $this->planSelected and ($this->amountToInvest or empty($this->amountToInvest))) {
            $this->disabled = '';
        } else {
            $this->disabled = 'disabled';
        }
    }

    public function checkIfAmountIsEmpty()
    {
        if ($this->paymentMethod and $this->planSelected and ($this->amountToInvest or empty($this->amountToInvest))) {
            $this->disabled = '';
        } else {
            $this->disabled = 'disabled';
        }
    }


    public function joinPlan()
    {
        
       $growthStart = now();

switch ($plan->increment_interval) {
    case "Monthly":
        $growthStart = $growthStart->addDays(25);
        break;
    case "Weekly":
        $growthStart = $growthStart->addDays(6);
        break;
    case "Daily":
        $growthStart = $growthStart->addHours(20);
        break;
    case "Hourly":
        $growthStart = $growthStart->addMinutes(49);
        break;
    case "Every 30 Minutes":
        $growthStart = $growthStart->addMinutes(19);
        break;
    default:
        $growthStart = $growthStart->addMinutes(4);
        break;
} 
        
        
        sleep(2);
        $this->feedback = 'Please wait';
        //get user
        $user = User::where('id', Auth::user()->id)->first();
        //get plan
        $plan = Plans::where('id', $this->planSelected->id)->first();
        // setup
        $expiration = explode(" ", $plan->expiration);
        $digit = $expiration[0];
        $frame = $expiration[1];
        $toexpire =  "add" . $frame;
        $end_at = Carbon::now()->$toexpire($digit)->toDateTimeString();

        if (empty($this->amountToInvest)) {
            session()->flash('message', 'Enter Amount to invest');
        } elseif (!$this->paymentMethod) {
            session()->flash('message', 'Choose a Payment Method');
        } elseif ($this->amountToInvest < $plan->min_price or $this->amountToInvest > $plan->max_price) {
            session()->flash('message', 'Amount too small or too large');
            $this->amountToInvest = 0;
        } else {
            if ($this->amountToInvest > 0) {
                $plan_price = $this->amountToInvest;
            } else {
                $plan_price = $plan->price;
            }
            //check if the user account balance can buy this plan
            if ($user->account_bal < $plan_price) {
                session()->flash('message', 'Your account is insufficient to purchase this plan. Please make a deposit.');
            } else {
                // Credit user the plan bonus
                if ($plan->gift > 0) {

                    User::where('id', $user->id)
                        ->update([
                            'bonus' => $user->bonus + $plan->gift,
                            'account_bal' => $user->account_bal + $plan->gift,
                        ]);

                    //create history
                    Tp_Transaction::create([
                        'user' => $user->id,
                        'plan' => $plan->name,
                        'amount' => $plan->gift,
                        'type' => "Gift Bonus",
                    ]);
                }
                //debit user
                if ($this->paymentMethod == "Account Balance") {
                    User::where('id', $user->id)
                        ->update([
                            'account_bal' => $user->account_bal - $plan_price,
                        ]);
                }

                //create history
                Tp_Transaction::create([
                    'user' => $user->id,
                    'plan' => $plan->name,
                    'amount' => $plan_price,
                    'type' => "Plan purchase",
                ]);

                // Set initial last_growth to NOW so ROI starts immediately based on interval
                $initialLastGrowth = now();

                //save user plan
                $userplanid = DB::table('user_plans')->insertGetId([
                    'plan' => $plan->id,
                    'user' => Auth::user()->id,
                    'amount' => $plan_price,
                    'active' => 'yes',
                    'inv_duration' => $plan->expiration,
                    'expire_date' => $end_at,
                    'activated_at' => Carbon::now(),
                    'last_growth' => $initialLastGrowth,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                User::where('id', Auth::user()->id)
                    ->update([
                        'plan' => $plan->id,
                        'user_plan' => $userplanid,
                        'entered_at' => Carbon::now(),
                    ]);

                //send notification
                $settings = Settings::where('id', '=', '1')->first();
                try {
                    $message = "This is to inform you that $user->name just purchased an investment plan: $plan->name";
                    $subject = "$user->name just purchased an investment plan";
                    Mail::to($settings->contact_email)->send(new NewNotification($message, $subject, 'Admin'));
                } catch (\Exception $e) {
                    \Log::error('Failed to send plan purchase email to admin: ' . $e->getMessage());
                }

                // Get a fresh instance of NotificationService when needed
                $notificationService = app(NotificationService::class);
                
                // Create in-app notification for plan purchase
                $notificationService->createInvestmentNotification(
                    $user, 
                    $plan->name, 
                    $settings->currency . number_format($plan_price, 2), 
                    route('myplans', ['sort' => 'yes'])
                );

                // If there was a gift bonus, create a notification for that too
                if ($plan->gift > 0) {
                    $formattedBonus = $settings->currency . number_format($plan->gift, 2);
                    $notificationService->createNotification(
                        $user->id,
                        "You received a gift bonus of {$formattedBonus} for purchasing the {$plan->name} plan",
                        "Investment Bonus",
                        'bonus',
                        route('accounthistory'),
                        'fa-gift',
                        'bg-tertiary/10'
                    );
                }

                session()->flash('success', 'You have successfully purchased a plan and your plan is now active.');
                $this->amountToInvest = 0;
                $this->disabled = 'disabled';
                $this->planSelected = Plans::orderByDesc('id')->first();
                $this->paymentMethod = 'Account Balance';
            }
        }
    }
}