<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Grievance extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'grievance_number',
        'title',
        'description',
        'category',
        'status',
        'priority',
        'resolution',
        'submitted_date',
        'resolution_date',
        'assigned_to',
    ];

    protected $casts = [
        'submitted_date'  => 'date',
        'resolution_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low'      => 'gray',
            'medium'   => 'blue',
            'high'     => 'amber',
            'critical' => 'red',
            default    => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'submitted'      => 'gray',
            'under_review'   => 'blue',
            'investigation'  => 'amber',
            'resolved'       => 'emerald',
            'closed'         => 'gray',
            'escalated'      => 'red',
            default          => 'gray',
        };
    }
}