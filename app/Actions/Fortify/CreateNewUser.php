<?php

namespace App\Actions\Fortify;

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Settings;
use App\Models\Agent;
use App\Models\CryptoAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        $settings = Settings::where('id', '1')->first();
        $request = request();
        
        // Determine which captcha to validate
        if ($settings->math_captcha == "true") {
            // Validate with Math Captcha
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'math_answer' => ['required', 'numeric'],
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
            
            // Verify math captcha answer
            $correctAnswer = session('math_captcha_answer');
            if (!$correctAnswer || $input['math_answer'] != $correctAnswer) {
                // Clear session on every attempt to prevent reuse
                session()->forget(['math_captcha_num1', 'math_captcha_num2', 'math_captcha_answer']);
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'math_answer' => ['The answer is incorrect. Please try again.'],
                ]);
            }
            // Clear session after successful validation
            session()->forget(['math_captcha_num1', 'math_captcha_num2', 'math_captcha_answer']);
            
        } elseif ($settings->captcha == "true") {
            // Validate with Google reCAPTCHA
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'g-recaptcha-response' => 'required|captcha',
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
        } else {
            // No captcha validation
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'unique:users,username'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
        }


        if (session('ref_by')) {
            $ref_by = session('ref_by');
            $user = User::where('username', $ref_by)->first();
            $ref_by_id = $user->id;
        } else {
            if (!empty($input['ref_by'])) {
                $sponsor = User::where('username', $input['ref_by'])->first();
                $ref_by_id = $sponsor->id;
            } else {
                $ref_by_id = NULL;
            }
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'username' => $input['username'],
            'country' => $input['country'],
            'ref_by' => $ref_by_id,
            'status' => 'active',
            'password' => Hash::make($input['password']),
        ]);

        $cryptoaccnt = new CryptoAccount();
        $cryptoaccnt->user_id = $user->id;
        $cryptoaccnt->save();
        $request->session()->forget('ref_by');
        
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return $user;
    }
}