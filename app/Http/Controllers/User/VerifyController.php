<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\KycApplicationRequest;
use App\Mail\NewNotification;
use App\Models\Kyc;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

class VerifyController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    //
    public function verifyaccount(KycApplicationRequest $request)
    {
        $user = Auth::user();
        $whitelist = array('jpeg', 'jpg', 'png');

        // filter front of document upload
        $frontimg = $request->file('frontimg');
        $backimg = $request->file('backimg');
        $backimgExtention = $backimg->extension();
        $extension = $frontimg->extension();

        if (!in_array($extension, $whitelist) or !in_array($backimgExtention, $whitelist)) {
            return redirect()->back()
                ->with('message', 'Unaccepted Image Uploaded, please make sure to upload the correct document.');
        }

        // upload documents to storage
        $frontimgPath = $frontimg->store('uploads', 'public');
        $backimgPath = $backimg->store('uploads', 'public');

        $kyc = new Kyc();
        $kyc->first_name = $request->first_name;
        $kyc->last_name = $request->last_name;
        $kyc->email = $request->email;
        $kyc->phone_number = $request->phone_number;
        $kyc->dob = $request->dob;
        $kyc->social_media = $request->social_media;
        $kyc->address = $request->address;
        $kyc->city = $request->city;
        $kyc->state = $request->state;
        $kyc->country = $request->country;
        $kyc->document_type = $request->document_type;
        $kyc->frontimg = $frontimgPath;
        $kyc->backimg = $backimgPath;
        $kyc->status = 'Under review';
        $kyc->user_id = $user->id;
        $kyc->save();


        //update user
        User::where('id', $user->id)
            ->update([
                'kyc_id' => $kyc->id,
                'account_verify' => 'Under review',
            ]);

        $settings = Settings::find(1);
        $message = "This is to inform you that $user->name just submitted a request for KYC(identity verification), please login your admin account to review and take neccessary action.";
        $subject = "Identity Verification Request from $user->name";
        $url = config('app.url') . '/admin/dashboard/kyc';
        
        try {
            Mail::to($settings->contact_email)->send(new NewNotification($message, $subject, 'Admin', $url));
        } catch (\Exception $e) {
            \Log::error('Failed to send KYC submission email to admin: ' . $e->getMessage());
        }

        // Create in-app notification for KYC submission
        $this->notificationService->createNotification(
            $user->id,
            "Your identity verification documents have been submitted successfully and are currently under review. We'll notify you once the verification is complete.",
            "KYC Verification Submitted",
            'info',
            route('account.verify'),
            'fa-shield',
            'bg-primary/10'
        );

        return redirect()->back()->with('success', 'Action Sucessful! Please wait while we verify your application. You will receive an email regarding the status of your application.');
    }

    // Add a method to handle KYC approval notification (to be called from admin controller)
    public function notifyKycApproval($userId)
    {
        $user = User::find($userId);

        if ($user) {
            // Create in-app notification for KYC approval
            $this->notificationService->createNotification(
                $user->id,
                "Congratulations! Your identity verification has been approved. You now have full access to all platform features.",
                "KYC Verification Approved",
                'success',
                route('dashboard'),
                'fa-shield-check',
                'bg-secondary/10'
            );

            return true;
        }

        return false;
    }

    // Add a method to handle KYC rejection notification (to be called from admin controller)
    public function notifyKycRejection($userId, $reason = null)
    {
        $user = User::find($userId);

        if ($user) {
            // Create in-app notification for KYC rejection
            $message = "Your identity verification was not approved.";
            if ($reason) {
                $message .= " Reason: $reason";
            }
            $message .= " Please submit new documents.";

            $this->notificationService->createNotification(
                $user->id,
                $message,
                "KYC Verification Rejected",
                'danger',
                route('account.verify'),
                'fa-shield-xmark',
                'bg-danger/10'
            );

            return true;
        }

        return false;
    }
}
