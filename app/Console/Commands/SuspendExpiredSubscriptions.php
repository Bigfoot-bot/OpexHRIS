<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FacilitySubscription;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SuspendExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Suspend expired subscriptions and send reminders';

    public function handle()
    {
        $settings = SystemSetting::getSettings();

        // Send 7-day reminder
        $expiringSoon = FacilitySubscription::where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays(7)->toDateString())
            ->whereDate('end_date', '>', now()->toDateString())
            ->whereNull('reminder_sent_at')
            ->get();

        foreach ($expiringSoon as $sub) {
            try {
                $tenant = DB::table('tenants')->where('id', $sub->tenant_id)->first();
                if ($tenant && $tenant->email) {
                    Mail::raw(
                        "Dear {$tenant->name},\n\nYour subscription expires in 7 days on " . $sub->end_date->format('M d, Y') . ".\n\nPlease renew to avoid service interruption.",
                        function ($m) use ($tenant) {
                            $m->to($tenant->email)->subject('Subscription Expiring Soon - OpEx HRIS');
                        }
                    );
                }
                $sub->update(['reminder_sent_at' => now()]);
                $this->info("Reminder sent to: {$tenant->name}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder: " . $e->getMessage());
            }
        }

        // Suspend expired subscriptions (after grace period)
        $graceDays = $settings->grace_period_days ?? 7;
        $expired = FacilitySubscription::whereIn('status', ['active', 'trial'])
            ->whereDate('end_date', '<', now()->subDays($graceDays)->toDateString())
            ->get();

        foreach ($expired as $sub) {
            $sub->update([
                'status'       => 'suspended',
                'suspended_at' => now(),
            ]);

            try {
                $tenant = DB::table('tenants')->where('id', $sub->tenant_id)->first();
                if ($tenant && $tenant->email) {
                    Mail::raw(
                        "Dear {$tenant->name},\n\nYour subscription has expired and your account has been suspended.\n\nPlease renew your subscription to restore access.",
                        function ($m) use ($tenant) {
                            $m->to($tenant->email)->subject('Subscription Suspended - OpEx HRIS');
                        }
                    );
                }
                $this->info("Suspended: {$tenant->name}");
            } catch (\Exception $e) {
                $this->error("Failed to notify: " . $e->getMessage());
            }
        }

        $this->info('Subscription check completed!');
    }
}

