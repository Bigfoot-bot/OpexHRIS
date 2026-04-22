<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_type',
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public static function log(
        string $action,
        string $module,
        string $description,
        array $oldValues = [],
        array $newValues = []
    ): void {
        $user = auth()->user() ?? auth('super_admin')->user();
        $tenantId = null;

        try {
            $tenantId = tenant('id');
        } catch (\Exception $e) {
            $tenantId = null;
        }

        self::create([
            'tenant_id'   => $tenantId,
            'user_type'   => auth()->check() ? 'tenant_user' : 'super_admin',
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'action'      => $action,
            'module'      => $module,
            'description' => $description,
            'old_values'  => !empty($oldValues) ? $oldValues : null,
            'new_values'  => !empty($newValues) ? $newValues : null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}