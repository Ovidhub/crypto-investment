<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

/**
 * ============================================================================
 * ClearCacheController - SECURITY HARDENED
 * ============================================================================
 * 
 * PURPOSE: Admin cache management functionality
 * 
 * SECURITY FIXES (Jan 22, 2026):
 * ✅ Removed saveLicense() backdoor method (was calling localhost:8080)
 * ✅ Changed cache clear to POST method (CSRF protection)
 * ✅ Method properly protected by admin middleware
 * 
 * SECURITY NOTES:
 * - Only admin users can clear cache (middleware protection)
 * - Uses standard Laravel Artisan commands
 * - No external communication
 * - No data exfiltration
 * 
 * WARNING: Do not re-add external API calls or licensing functionality
 * ============================================================================
 */
class ClearCacheController extends Controller
{
    /**
     * Clear application cache (view and general cache)
     * 
     * SECURITY: Protected by admin middleware, uses POST for CSRF
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearcache()
    {
        try {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            
            return redirect()->back()->with('success', 'Cache cleared successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}