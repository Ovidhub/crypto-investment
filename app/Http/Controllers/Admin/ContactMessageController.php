<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        // Mark all as read when the admin opens the inbox.
        ContactMessage::where('read_by_admin', false)->update(['read_by_admin' => true]);

        return view('admin.contact-messages', [
            'title'    => 'Contact Messages',
            'messages' => ContactMessage::orderByDesc('id')->paginate(20),
        ]);
    }

    public function destroy($id)
    {
        ContactMessage::findOrFail($id)->delete();

        return back()->with('success', 'Message deleted.');
    }
}
