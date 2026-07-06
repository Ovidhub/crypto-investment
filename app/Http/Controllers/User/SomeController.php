<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SomeController extends Controller
{
    public function changetheme(Request $request)
    {
        // Change theme logic
        return back()->with('success', 'Theme changed successfully');
    }
}
