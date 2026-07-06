<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Settings;

class VerificationCodeController extends Controller
{
    /**
     * Display the verification code form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.verify-code');
    }

    /**
     * Send a new verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        $user = Auth::user();
        
        // Check if verification code was recently sent (avoid spam)
        if ($user->verification_code_expires_at && Carbon::parse($user->verification_code_expires_at)->gt(Carbon::now())) {
            $timeLeft = Carbon::now()->diffInMinutes(Carbon::parse($user->verification_code_expires_at));
            return back()->with('error', "Please wait {$timeLeft} minutes before requesting a new code.");
        }
        
        // Generate a new 6-digit code
        $code = mt_rand(100000, 999999);
        
        $user->verification_code = $code;
        $user->verification_code_expires_at = Carbon::now()->addMinutes(5);
        $user->verification_attempts = 0;
        $user->save();
        
        // Send the code via email
        $this->sendVerificationEmail($user, $code);
        
        return back()->with('success', 'Verification code has been sent to your email.');
    }

    /**
     * Verify the email verification code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|min:6|max:6',
        ]);
        
        $user = Auth::user();
        
        // Check if verification code has expired
        if (!$user->verification_code_expires_at || Carbon::parse($user->verification_code_expires_at)->lt(Carbon::now())) {
            return back()->with('error', 'Verification code has expired. Please request a new one.');
        }
        
        // Check if too many failed attempts
        if ($user->verification_attempts >= 5) {
            return back()->with('error', 'Too many failed attempts. Please request a new code.');
        }
        
        // Verify the code
        if ($request->code == $user->verification_code) {
            // Mark email as verified
            $user->email_verified_at = Carbon::now();
            $user->verification_code = null;
            $user->verification_code_expires_at = null;
            $user->verification_attempts = 0;
            $user->save();
            
            return redirect()->route('dashboard');
        }
        
        // Increment failed attempts
        $user->verification_attempts += 1;
        $user->save();
        
        return back()->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Send verification email with code.
     *
     * @param  \App\Models\User  $user
     * @param  string  $code
     * @return void
     */
    protected function sendVerificationEmail(User $user, $code)
    {
        $settings = Settings::where('id', 1)->first();
        
        // Here you would use your email template
        // This is a basic example
        $data = [
            'name' => $user->name,
            'code' => $code,
            'site_name' => $settings->site_name ?? 'Our Platform',
            'expires_in' => '5 minutes',
        ];
        
        try {
            Mail::send('emails.verify-code', $data, function($message) use ($user, $settings) {
                $message->to($user->email, $user->name)
                        ->subject('Verify Your Email Address');
                
                if ($settings && $settings->site_email) {
                    $message->from($settings->site_email, $settings->site_name ?? 'Our Platform');
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send verification code email: ' . $e->getMessage());
        }
    }
} 