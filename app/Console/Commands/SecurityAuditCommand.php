<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Activity;
use App\Models\Admin;
use Carbon\Carbon;

class SecurityAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:audit {--email : Send report via email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run security audit and generate report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔒 Running Security Audit...');
        $this->newLine();

        $issues = [];

        // Check 1: Environment Configuration
        $this->info('Checking environment configuration...');
        if (config('app.debug') === true) {
            $issues[] = 'CRITICAL: APP_DEBUG is enabled in production';
            $this->error('  ❌ APP_DEBUG is enabled');
        } else {
            $this->line('  ✅ APP_DEBUG is disabled');
        }

        if (config('app.env') !== 'production') {
            $issues[] = 'WARNING: APP_ENV is not set to production';
            $this->warn('  ⚠️  APP_ENV is not production');
        } else {
            $this->line('  ✅ APP_ENV is production');
        }

        // Check 2: Database Password
        $this->newLine();
        $this->info('Checking database security...');
        if (empty(config('database.connections.mysql.password'))) {
            $issues[] = 'CRITICAL: Database password is empty';
            $this->error('  ❌ Database password is empty');
        } else {
            $this->line('  ✅ Database password is set');
        }

        if (config('database.connections.mysql.username') === 'root') {
            $issues[] = 'WARNING: Using root database user';
            $this->warn('  ⚠️  Using root user for database');
        } else {
            $this->line('  ✅ Using dedicated database user');
        }

        // Check 3: Session Configuration
        $this->newLine();
        $this->info('Checking session configuration...');
        $sessionLifetime = config('session.lifetime');
        if ($sessionLifetime > 480) {
            $issues[] = "WARNING: Session lifetime is very long ({$sessionLifetime} minutes)";
            $this->warn("  ⚠️  Session lifetime is {$sessionLifetime} minutes");
        } else {
            $this->line("  ✅ Session lifetime is {$sessionLifetime} minutes");
        }

        // Check 4: Failed Login Attempts
        $this->newLine();
        $this->info('Checking for suspicious activities...');
        
        // Check failed admin logins in last 24 hours
        $failedLogins = Activity::where('type', 'Failed Admin Login')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        if ($failedLogins > 10) {
            $issues[] = "WARNING: {$failedLogins} failed admin login attempts in last 24 hours";
            $this->warn("  ⚠️  {$failedLogins} failed admin logins");
        } else {
            $this->line("  ✅ {$failedLogins} failed admin logins (normal)");
        }

        // Check 5: Admin Accounts
        $this->newLine();
        $this->info('Checking admin accounts...');
        $adminCount = Admin::count();
        $this->line("  ℹ️  {$adminCount} admin accounts found");
        
        if ($adminCount > 5) {
            $issues[] = "WARNING: {$adminCount} admin accounts exist";
            $this->warn("  ⚠️  Consider reviewing admin accounts");
        }

        // Check 6: File Permissions (if possible)
        $this->newLine();
        $this->info('Checking file permissions...');
        if (is_writable(base_path('.env'))) {
            $envPerms = substr(sprintf('%o', fileperms(base_path('.env'))), -4);
            if ($envPerms !== '0400' && $envPerms !== '0600') {
                $issues[] = "WARNING: .env file permissions are {$envPerms} (should be 400 or 600)";
                $this->warn("  ⚠️  .env permissions: {$envPerms}");
            } else {
                $this->line("  ✅ .env permissions: {$envPerms}");
            }
        }

        // Check 7: Storage Permissions
        if (!is_writable(storage_path())) {
            $issues[] = 'CRITICAL: Storage directory is not writable';
            $this->error('  ❌ Storage directory not writable');
        } else {
            $this->line('  ✅ Storage directory is writable');
        }

        // Generate Report
        $this->newLine(2);
        $this->info('📊 Security Audit Report');
        $this->line(str_repeat('=', 50));
        
        if (empty($issues)) {
            $this->info('✅ No security issues found!');
        } else {
            $this->error('⚠️  Security issues detected:');
            $this->newLine();
            foreach ($issues as $index => $issue) {
                $this->line(($index + 1) . '. ' . $issue);
            }
        }

        $this->newLine();
        $this->line('Report generated: ' . now()->format('Y-m-d H:i:s'));
        $this->line(str_repeat('=', 50));

        // Log the audit
        Log::channel('security')->info('Security audit completed', [
            'issues_found' => count($issues),
            'issues' => $issues,
            'timestamp' => now()
        ]);

        // Send email if requested
        if ($this->option('email')) {
            $this->info('📧 Sending report via email...');
            // TODO: Implement email notification
            $this->line('  ⚠️  Email notification not yet implemented');
        }

        return empty($issues) ? 0 : 1;
    }
}
