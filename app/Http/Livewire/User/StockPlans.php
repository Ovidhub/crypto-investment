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

class StockPlans extends Component
{
    public $amountToInvest = 0;
    public $selectedPlanId = null;
    public $paymentMethod;
    public $feedback = '';

    public function mount()
    {
        $this->paymentMethod = 'Account Balance';
    }

    public function render()
    {
        return view('livewire.user.stock-plans', [
            'stockPlans' => Plans::where('plan_type', 'stock')->orderByDesc('id')->get(),
        ]);
    }

    public function purchaseStock($planId)
    {
        $this->selectedPlanId = $planId;
        $plan = Plans::find($planId);
        
        if (!$plan || $plan->plan_type !== 'stock') {
            $this->feedback = 'Invalid stock plan selected.';
            return;
        }

        $amount = floatval($this->amountToInvest);
        
        // Validate amount
        if ($amount < $plan->min_price) {
            $this->feedback = 'Minimum investment is ' . Settings::first()->currency . number_format($plan->min_price, 2);
            return;
        }

        if ($amount > $plan->max_price) {
            $this->feedback = 'Maximum investment is ' . Settings::first()->currency . number_format($plan->max_price, 2);
            return;
        }

        // Check user balance
        $user = Auth::user();
        if ($this->paymentMethod === 'Account Balance' && $user->account_bal < $amount) {
            $this->feedback = 'Insufficient account balance.';
            return;
        }

        try {
            DB::beginTransaction();

            // Deduct from user balance
            if ($this->paymentMethod === 'Account Balance') {
                $user->account_bal -= $amount;
                $user->save();
            }

            // Create transaction record
            $transaction = new Tp_Transaction();
            $transaction->plan = $plan->name;
            $transaction->user = $user->id;
            $transaction->amount = $amount;
            $transaction->type = 'Investment';
            $transaction->status = 'Processed';
            $transaction->save();

            // Calculate ROI details
            $durationDays = $this->getDurationInDays($plan->expiration);
            $expectedProfit = ($amount * $plan->expected_return) / 100;

            // Add to user's plan
            DB::table('user_plans')->insert([
                'plan_id' => $plan->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'active' => 'yes',
                'activated_at' => Carbon::now(),
                'last_growth' => Carbon::now(),
                'next_growth' => $this->calculateNextGrowth($plan->increment_interval),
                'expire_date' => Carbon::now()->addDays($durationDays),
                'capital_withdraw' => 'No',
                'expected_profit' => $expectedProfit,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Send notification email
            try {
                $settings = Settings::first();
                $emailData = [
                    'subject' => 'Stock Investment Purchase Successful',
                    'message' => "You have successfully invested {$settings->currency}{$amount} in {$plan->stock_company} ({$plan->stock_symbol}). Expected return: {$plan->expected_return}% over {$plan->expiration}.",
                ];
                Mail::to($user->email)->send(new NewNotification($emailData));
            } catch (\Exception $e) {
                \Log::error('Stock investment notification email failed: ' . $e->getMessage());
            }

            DB::commit();

            $this->feedback = 'Stock investment successful! You have invested in ' . $plan->stock_company;
            $this->amountToInvest = 0;
            $this->selectedPlanId = null;

            $this->emit('investmentComplete');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->feedback = 'Investment failed. Please try again.';
            \Log::error('Stock investment failed: ' . $e->getMessage());
        }
    }

    private function getDurationInDays($expiration)
    {
        $parts = explode(' ', $expiration);
        $value = intval($parts[0]);
        $unit = strtolower($parts[1] ?? 'months');

        switch ($unit) {
            case 'day':
            case 'days':
                return $value;
            case 'week':
            case 'weeks':
                return $value * 7;
            case 'month':
            case 'months':
                return $value * 30;
            case 'year':
            case 'years':
                return $value * 365;
            default:
                return 30;
        }
    }

    private function calculateNextGrowth($interval)
    {
        switch (strtolower($interval)) {
            case 'daily':
                return Carbon::now()->addDay();
            case 'weekly':
                return Carbon::now()->addWeek();
            case 'monthly':
                return Carbon::now()->addMonth();
            case 'hourly':
                return Carbon::now()->addHour();
            default:
                return Carbon::now()->addDay();
        }
    }
}
