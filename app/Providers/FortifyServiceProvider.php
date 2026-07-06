<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings;
//use App\Models\TermsPrivacy;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Custom response after successful registration
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                // Get the user from request - Fortify should have logged them in
                $user = $request->user();
                
                // Ensure user is authenticated
                if (!$user) {
                    \Log::warning('User not authenticated after registration');
                    return redirect()->route('login')
                        ->with('success', 'Registration successful! Please log in to continue.');
                }
                
                // Check if email verification is enabled
                $features = config('fortify.features', []);
                $emailVerificationEnabled = false;
                
                foreach ($features as $feature) {
                    if (is_string($feature) && str_contains($feature, 'emailVerification')) {
                        $emailVerificationEnabled = true;
                        break;
                    }
                    if ($feature instanceof \Laravel\Fortify\Features && method_exists($feature, '__toString')) {
                        if (str_contains((string)$feature, 'emailVerification')) {
                            $emailVerificationEnabled = true;
                            break;
                        }
                    }
                }
                
                // If email verification is enabled and email not verified
                if ($emailVerificationEnabled && !$user->hasVerifiedEmail()) {
                    \Log::info('Redirecting to verification notice for user: ' . $user->email);
                    
                    return redirect()->route('verification.notice')
                        ->with('success', 'Registration successful! Please check your email (' . $user->email . ') and click the verification link to access your dashboard.');
                }
                
                // Check if KYC is required on registration
                $settings = \App\Models\Settings::find(1);
                if ($settings && $settings->enable_kyc_registration == 'yes' && $user->account_verify != 'Verified') {
                    \Log::info('Redirecting to KYC verification for user: ' . $user->email);
                    
                    return redirect()->route('account.verify')
                        ->with('success', 'Registration successful! Please complete your account verification to access all features.');
                }
                
                // No verification needed - go to dashboard
                return redirect('/dashboard')
                    ->with('success', 'Registration successful! Welcome to your dashboard.');
            }
        });

        // Custom response after successful login
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect()->intended('/dashboard');
            }
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge', [
                'title' => 'Two Factor Authentication',
            ]);
        });

        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password', [
                'title' => 'Password Confirmation',
            ]);
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('auth.forgot-password',[
                'title' => 'Enter email to reset your password',
            ]);
        });

        // Fortify::registerView(function () {
        //     return view('auth.registers',[
        //         'terms' => TermsPrivacy::find(1),
        //     ]);
        // });
    }
}
