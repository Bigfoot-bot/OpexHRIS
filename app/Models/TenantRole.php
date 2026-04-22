<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantRole extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'is_admin'];

    protected $casts = ['is_admin' => 'boolean'];

    public function permissions()
    {
        return $this->hasMany(TenantPermission::class, 'role_id');
    }

    public function users()
    {
        return $this->hasMany(TenantUserRole::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('permission', $permission)->exists();
    }

    public static function allPermissions(): array
    {
        return [
            'manage_employees'   => 'Manage Employees',
            'manage_leave'       => 'Manage Leave',
            'manage_payroll'     => 'Manage Payroll',
            'manage_documents'   => 'Manage Documents',
            'manage_assets'      => 'Manage Assets',
            'manage_contracts'   => 'Manage Contracts',
            'manage_branches'    => 'Manage Branches',
            'manage_recruitment' => 'Manage Recruitment',
            'manage_performance' => 'Manage Performance',
            'manage_reports'     => 'Manage Reports',
            'manage_settings'    => 'Manage Settings',
            'manage_users'       => 'Manage Users',
            'manage_announcements' => 'Manage Announcements',
            'view_payroll'       => 'View Payroll',
            'view_reports'       => 'View Reports',
        ];
    }
}
