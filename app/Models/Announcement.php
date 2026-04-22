<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'send_email' => 'boolean',
    ];

    public function scopeGlobal($query)
    {
        return $query->where('type', 'global');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId)->where('type', 'facility');
    }
}


