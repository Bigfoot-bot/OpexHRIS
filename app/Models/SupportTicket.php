<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Central\Tenant;

class SupportTicket extends Model
{
    protected $fillable = [
        'tenant_id',
        'subject',
        'message',
        'status',
        'priority',
        'category',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'        => 'bg-amber-50 text-amber-600',
            'in_progress' => 'bg-blue-50 text-blue-600',
            'resolved'    => 'bg-emerald-50 text-emerald-600',
            'closed'      => 'bg-gray-50 text-gray-500',
            default       => 'bg-gray-50 text-gray-500',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low'    => 'bg-gray-50 text-gray-500',
            'medium' => 'bg-blue-50 text-blue-600',
            'high'   => 'bg-amber-50 text-amber-600',
            'urgent' => 'bg-red-50 text-red-500',
            default  => 'bg-gray-50 text-gray-500',
        };
    }
}