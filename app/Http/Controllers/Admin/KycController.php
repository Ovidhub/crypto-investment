<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\NewNotification;
use App\Models\Kyc;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\User\VerifyController;
use App\Services\NotificationService;

class KycController extends Controller
{
    protected $notificationService;
    protected $verifyController;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->verifyController = new VerifyController($notificationService);
    }

    public function processKyc(Request $request)
    {
        $application = Kyc::find($request->kyc_id);
        $user = User::where('id', $application->user_id)->first();

        // will use API key
        if ($request->action == 'Accept') {
            User::where('id', $user->id)
                ->update([
                    'account_verify' => 'Verified',
                ]);
            $application->status = "Verified";
            $application->save();
            
            // Create notification for KYC approval
            $this->verifyController->notifyKycApproval($user->id);
        } else {
            if (Storage::disk('public')->exists($application->frontimg) and Storage::disk('public')->exists($application->backimg)) {
                Storage::disk('public')->delete($application->frontimg);
                Storage::disk('public')->delete($application->backimg);
            }

            // Update the user verification status
            $user->account_verify = 'Rejected';
            $user->save();
            // delete the application form database so user can resubmit application
            $application->delete();
            
            // Create notification for KYC rejection with the provided reason
            $this->verifyController->notifyKycRejection($user->id, $request->message);
        }

        try {
            Mail::to($user->email)->send(new NewNotification($request->message, $request->subject, $user->name));
        } catch (\Exception $e) {
            \Log::error('Failed to send KYC status email to user: ' . $e->getMessage());
        }
        
        return redirect()->route('kyc')->with('success', 'Action Sucessful!');
    }
}