<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function forgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendPasswordRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        $admin = Admin::where('email', $request->email)->first();
        
        if ($admin) {
            $token = Str::random(60);
            $admin->remember_token = $token;
            $admin->save();

            // Send email with reset link
            try {
                Mail::send('emails.admin-password-reset', ['token' => $token, 'email' => $request->email], function($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Admin Password Reset Request');
                });
                
                return back()->with('success', 'Password reset link has been sent to your email.');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to send reset email. Please try again.');
            }
        }

        return back()->with('error', 'Email not found.');
    }

    public function resetPassword($email)
    {
        return view('admin.auth.reset-password', compact('email'));
    }

    public function validateResetPasswordToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)
                     ->where('remember_token', $request->token)
                     ->first();

        if (!$admin) {
            return back()->with('error', 'Invalid reset token or email.');
        }

        $admin->password = Hash::make($request->password);
        $admin->remember_token = null;
        $admin->save();

        return redirect()->route('adminloginform')->with('success', 'Password has been reset successfully.');
    }
}
