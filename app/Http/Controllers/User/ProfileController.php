<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\NotificationService;

class ProfileController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    //Updating Profile Route
    public function updateprofile(Request $request)
    {
        User::where('id', Auth::user()->id)
            ->update([
                'name' => $request->name,
                'dob' => $request->dob,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
            
        // Create notification for profile update
        $this->notificationService->createNotification(
            Auth::user()->id,
            "Your profile information has been updated successfully.",
            "Profile Updated",
            'info',
            route('profile'),
            'fa-user',
            'bg-primary/10'
        );
        
        return response()->json(['status' => 200, 'success' => 'Profile Information Updated Sucessfully!']);
    }

    //update account and contact info
    public function updateacct(Request $request)
    {
        User::where('id', Auth::user()->id)
            ->update([
                'bank_name' => $request['bank_name'],
                'account_name' => $request['account_name'],
                'account_number' => $request['account_no'],
                'swift_code' => $request['swiftcode'],
                'btc_address' => $request['btc_address'],
                'eth_address' => $request['eth_address'],
                'ltc_address' => $request['ltc_address'],
                'usdt_address' => $request['usdt_address'],
            ]);
            
        // Create notification for withdrawal info update
        $this->notificationService->createNotification(
            Auth::user()->id,
            "Your withdrawal information has been updated successfully.",
            "Withdrawal Info Updated",
            'info',
            route('profile'),
            'fa-credit-card',
            'bg-primary/10'
        );
        
        return response()->json(['status' => 200, 'success' => 'Withdrawal Info updated Sucessfully']);
    }

    //Update Password
    public function updatepass(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::find(Auth::user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('message', 'Current password does not match!');
        }
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Create notification for password update
        $this->notificationService->createNotification(
            $user->id,
            "Your account password has been changed successfully. If you didn't make this change, please contact support immediately.",
            "Password Changed",
            'warning',
            route('twofa'),
            'fa-key',
            'bg-warning/10'
        );
        
        return back()->with('success', 'Password updated successfully');
    }

    // Update email preference logic
    public function updateemail(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $user->sendotpemail = $request->otpsend;
        $user->sendroiemail = $request->roiemail;
        $user->sendinvplanemail = $request->invplanemail;
        $user->save();
        
        // Create notification for email preferences update
        $this->notificationService->createNotification(
            $user->id,
            "Your email notification preferences have been updated successfully.",
            "Email Preferences Updated",
            'info',
            route('profile'),
            'fa-envelope',
            'bg-primary/10'
        );
        
        return response()->json(['status' => 200, 'success' => 'Email Preference updated']);
    }
}