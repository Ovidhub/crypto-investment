<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::first();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Settings::first();
        
        if ($settings) {
            $settings->update($request->all());
            return back()->with('success', 'Settings updated successfully');
        }
        
        return back()->with('error', 'Settings not found');
    }
}
