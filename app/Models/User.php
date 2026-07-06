<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use App\Models\Settings;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
   
    /**
     * Send the email verification notification.
     *
     * @return void
     */

    public function sendEmailVerificationNotification()
    {
        $settings = Settings::where('id', 1)->first();

        if ($settings->enable_verification == 'true') {
            // Generate and save verification code instead of sending a link
            $code = mt_rand(100000, 999999);
            $this->verification_code = $code;
            $this->verification_code_expires_at = Carbon::now()->addMinutes(30);
            $this->verification_attempts = 0;
            $this->save();
            
            // Send the verification code email
            $this->sendVerificationCodeEmail($code);
        }
    }
    
    /**
     * Send verification code email.
     *
     * @param string $code
     * @return void
     */
    protected function sendVerificationCodeEmail($code)
    {
        $settings = Settings::where('id', 1)->first();
        
        $data = [
            'name' => $this->name,
            'code' => $code,
            'site_name' => $settings->site_name ?? 'Our Platform',
            'expires_in' => '30 minutes',
        ];
        
        Mail::send('emails.verify-code', $data, function($message) use ($settings) {
            $message->to($this->email, $this->name)
                    ->subject('Verify Your Email Address');
            
            if ($settings && $settings->site_email) {
                $message->from($settings->site_email, $settings->site_name ?? 'Our Platform');
            }
        });
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'l_name', 'email', 'phone','country','password', 'ref_by','status', 'username', 'email_verified_at',
        'verification_code', 'verification_code_expires_at', 'verification_attempts',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function dp(){
    	return $this->hasMany(Deposit::class, 'user');
    }

    public function wd(){
    	return $this->hasMany(Withdrawal::class, 'user');
    }

    public function tuser(){
    	return $this->belongsTo(Admin::class, 'assign_to');
    }
    
    public function dplan(){
    	return $this->belongsTo(Plans::class, 'plan');
    }

    public function plans(){
        return $this->hasMany(User_plans::class,'user', 'id');
    }

    /**
     * User's notifications relationship
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get count of unread notifications
     *
     * @return int
     */
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public static function search($search): \Illuminate\Database\Eloquent\Builder
    {
        return empty($search) ? static::query()
        : static::query()->where('id', 'like', '%'.$search.'%')
        ->orWhere('name', 'like', '%'.$search.'%')
        ->orWhere('username', 'like', '%'.$search.'%')
        ->orWhere('email', 'like', '%'.$search.'%');
    }
	
    /**
     * User's trading watchlists relationship
     */
    public function tradingWatchlists()
    {
        return $this->hasMany(TradingWatchlist::class);
    }
    
    /**
     * User's trading preferences relationship
     */
    public function tradingPreferences()
    {
        return $this->hasMany(TradingPreference::class);
    }
    
    /**
     * User's trading orders relationship
     */
    public function tradingOrders()
    {
        return $this->hasMany(TradingOrder::class);
    }
}
