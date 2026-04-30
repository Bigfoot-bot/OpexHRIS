<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes for high-traffic queries.
 * Each index is added in its own try-catch so existing indexes are skipped silently.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->addIndex('super_admins', fn (Blueprint $t) => $t->index('email'));

        $this->addIndex('tenants',  fn (Blueprint $t) => $t->index('created_at'));

        $this->addIndex('domains',  fn (Blueprint $t) => $t->index('domain'));
        $this->addIndex('domains',  fn (Blueprint $t) => $t->index('tenant_id'));

        $this->addIndex('users',    fn (Blueprint $t) => $t->index('email'));

        if (Schema::hasColumn('employees', 'status')) {
            $this->addIndex('employees', fn (Blueprint $t) => $t->index('status'));
        }
        if (Schema::hasColumn('employees', 'department')) {
            $this->addIndex('employees', fn (Blueprint $t) => $t->index('department'));
        }
        $this->addIndex('employees', fn (Blueprint $t) => $t->index('created_at'));

        if (Schema::hasTable('leave_requests')) {
            $this->addIndex('leave_requests', fn (Blueprint $t) => $t->index(['employee_id', 'status']));
            $this->addIndex('leave_requests', fn (Blueprint $t) => $t->index('status'));
            $this->addIndex('leave_requests', fn (Blueprint $t) => $t->index('start_date'));
        }

        if (Schema::hasTable('payroll_records')) {
            $this->addIndex('payroll_records', fn (Blueprint $t) => $t->index('payroll_period_id'));
            $this->addIndex('payroll_records', fn (Blueprint $t) => $t->index('employee_id'));
        }

        if (Schema::hasTable('audit_logs')) {
            $this->addIndex('audit_logs', fn (Blueprint $t) => $t->index('created_at'));
            $this->addIndex('audit_logs', fn (Blueprint $t) => $t->index('user_id'));
            $this->addIndex('audit_logs', fn (Blueprint $t) => $t->index('action'));
        }

        if (Schema::hasTable('notifications')) {
            $this->addIndex('notifications', fn (Blueprint $t) => $t->index(['notifiable_type', 'notifiable_id', 'read_at']));
        }

        if (Schema::hasTable('jobs')) {
            $this->addIndex('jobs', fn (Blueprint $t) => $t->index(['queue', 'reserved_at']));
        }
    }

    public function down(): void
    {
        $drops = [
            'super_admins'   => ['super_admins_email_index'],
            'tenants'        => ['tenants_created_at_index'],
            'domains'        => ['domains_domain_index', 'domains_tenant_id_index'],
            'users'          => ['users_email_index'],
            'employees'      => ['employees_status_index', 'employees_department_index', 'employees_created_at_index'],
            'leave_requests' => ['leave_requests_employee_id_status_index', 'leave_requests_status_index', 'leave_requests_start_date_index'],
            'payroll_records'=> ['payroll_records_payroll_period_id_index', 'payroll_records_employee_id_index'],
            'audit_logs'     => ['audit_logs_created_at_index', 'audit_logs_user_id_index', 'audit_logs_action_index'],
            'notifications'  => ['notifications_notifiable_index'],
            'jobs'           => ['jobs_queue_reserved_at_index'],
        ];

        foreach ($drops as $table => $indexes) {
            if (!Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                foreach ($indexes as $index) {
                    try { $blueprint->dropIndex($index); } catch (\Exception) {}
                }
            });
        }
    }

    private function addIndex(string $table, \Closure $callback): void
    {
        if (!Schema::hasTable($table)) return;
        try {
            Schema::table($table, $callback);
        } catch (\Exception) {
            // Index already exists or column missing — skip silently
        }
    }
};
