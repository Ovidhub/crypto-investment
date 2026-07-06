<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\NewNotification;
use App\Models\Plans;
use App\Models\Settings;
use App\Models\Tp_Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StockInvestmentController extends Controller
{
    public function index()
    {
        $stockPlans = Plans::where('plan_type', 'stock')->orderByDesc('id')->get();
        $settings = Settings::first();

        return view('user.stockplans', compact('stockPlans', 'settings'));
    }

    public function purchase(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $plan = Plans::findOrFail($request->plan_id);
        
        // Verify it's a stock plan
        if ($plan->plan_type !== 'stock') {
            return back()->with('error', 'Invalid stock plan selected.');
        }

        $amount = floatval($request->amount);
        $user = Auth::user();
        $settings = Settings::first();
        
        // Validate amount range
        if ($amount < $plan->min_price) {
            return back()->with('error', 'Minimum investment is ' . $settings->currency . number_format($plan->min_price, 2));
        }

        if ($amount > $plan->max_price) {
            return back()->with('error', 'Maximum investment is ' . $settings->currency . number_format($plan->max_price, 2));
        }

        // Check user balance
        if ($user->account_bal < $amount) {
            return back()->with('error', 'Insufficient account balance.');
        }

        try {
            DB::beginTransaction();

            // Deduct from user balance
            $user->account_bal -= $amount;
            $user->save();

            // Calculate ROI details
            $durationDays = $this->getDurationInDays($plan->expiration);
            $expectedProfit = ($amount * $plan->expected_return) / 100;

            // Calculate expiration date
            $expiration = explode(" ", $plan->expiration);
            $digit = $expiration[0];
            $frame = $expiration[1];
            $toexpire = "add" . $frame;
            $end_at = Carbon::now()->$toexpire($digit)->toDateTimeString();

            // Create user plan entry (using correct column names)
            $userPlanId = DB::table('user_plans')->insertGetId([
                'plan' => $plan->id,
                'user' => $user->id,
                'amount' => $amount,
                'active' => 'yes',
                'inv_duration' => $plan->expiration,
                'activated_at' => Carbon::now(),
                'last_growth' => Carbon::now(),
                'expire_date' => $end_at,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create transaction record (without status field)
            $transaction = new Tp_Transaction();
            $transaction->plan = $plan->name;
            $transaction->user = $user->id;
            $transaction->amount = $amount;
            $transaction->type = 'Investment';
            $transaction->user_plan_id = $userPlanId;
            $transaction->save();

            // Send notification email
            try {
                $message = "You have successfully invested {$settings->currency}{$amount} in {$plan->stock_company} ({$plan->stock_symbol}). Expected return: {$plan->expected_return}% over {$plan->expiration}.";
                $subject = 'Stock Investment Purchase Successful';
                Mail::to($user->email)->send(new NewNotification($message, $subject, $user->name));
            } catch (\Exception $e) {
                Log::error('Stock investment notification email failed: ' . $e->getMessage());
            }

            DB::commit();

            return back()->with('success', 'Stock investment successful! You have invested in ' . $plan->stock_company);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock investment failed: ' . $e->getMessage());
            return back()->with('error', 'Investment failed: ' . $e->getMessage());
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
