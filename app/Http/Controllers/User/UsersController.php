<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Mail\NewNotification;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{

  public function verifyemail()
  {
    // Generate a verification code if not already generated
    if (!Auth::user()->verification_code) {
      Auth::user()->sendEmailVerificationNotification();
    }
    
    // Redirect to verification code page
    return redirect()->route('verification.code');
  }
  public function addusername(Request $request)
  {
    Validator::make($request, [
      'username' => ['required', 'unique:users,username'],
    ])->validate();

    User::where('id', Auth::user()->id)->update([
      'username' => $request['username'],
    ]);
    return redirect()->route('dashboard');
  }

  //send contact message to admin email
  public function sendcontact(Request $request)
  {
    // The public contact form posts: username, email, text.
    $data = $request->validate([
      'username' => 'required|string|max:120',
      'email'    => 'required|email|max:150',
      'text'     => 'required|string|max:5000',
    ], [
      'username.required' => 'Please enter your name.',
      'email.required'    => 'Please enter a valid email address.',
      'text.required'     => 'Please enter your message.',
    ]);

    // Always store the message so nothing is lost even if email delivery fails.
    $enquiry = \App\Models\ContactMessage::create([
      'name'       => $data['username'],
      'email'      => $data['email'],
      'message'    => $data['text'],
      'ip_address' => $request->ip(),
    ]);

    // Best-effort email notification to the site's contact address.
    $settings = Settings::where('id', '1')->first();
    if (!empty($settings->contact_email)) {
      try {
        $subject = "New contact message from {$enquiry->name} <{$enquiry->email}>";
        Mail::to($settings->contact_email)->send(new NewNotification($enquiry->message, $subject, 'Admin'));
      } catch (\Throwable $e) {
        \Log::warning('Contact form email failed (message still saved): ' . $e->getMessage());
      }
    }

    return redirect()->back()
      ->with('success', 'Thanks — your message has been received. Our team will get back to you shortly.');
  }


  //Get downlines level
  public function getdownlines($array, $parent = 0, $level = 0)
  {
    $referedMembers = '';
    foreach ($array as $entry) {
      if ($entry->ref_by == $parent) {

        if ($level == 0) {
          $levelQuote = "Direct referral";
        } else {
          $levelQuote = "Indirect referral level $level";
        }

        $referedMembers .= "
                  <tr>
                  <td> $entry->name $entry->l_name </td> 
                  <td> $levelQuote </td>" .
          '<td>' . $this->getUserParent($entry->id) . '</td>' .
          '<td>' . $this->getUserStatus($entry->id) . '</td>
                  <td>' . $this->getUserRegDate($entry->id) . '</td>
                  </tr>';

        $referedMembers .= $this->getdownlines($array, $entry->id, $level + 1);
      }

      if ($level == 6) {
        break;
      }
    }
    return $referedMembers;
  }

  //Get user Parent
  function getUserParent($id)
  {
    $user = User::where('id', $id)->first();
    $parent = User::where('id', $user->ref_by)->first();
    if ($parent) {
      return "$parent->name $parent->l_name";
    } else {
      return "null";
    }
  }

  //Get user status
  function getUserStatus($id)
  {
    $user = User::where('id', $id)->first();

    return $user->status;
  }

  //Get User Registration Date
  function getUserRegDate($id)
  {
    $user = User::where('id', $id)->first();

    return $user->created_at;
  }
}