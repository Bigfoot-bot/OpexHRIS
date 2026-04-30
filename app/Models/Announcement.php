<?php

namespace App\Models;

use App\Models\Central\Tenant;
use App\Models\Tenant\Employee;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'body',
        'meeting_link',
        'type',
        'sender_type',
        'tenant_id',
        'send_email',
        'branch_id',
        'employee_id',
    ];

    protected $casts = [
        'send_email' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeGlobal($query)
    {
        return $query->where('type', 'global');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId)->whereIn('type', ['facility', 'targeted']);
    }
}


