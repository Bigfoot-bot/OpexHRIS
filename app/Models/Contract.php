<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'tenant_id', 'employee_id', 'title', 'contract_type', 'start_date',
        'end_date', 'salary', 'department', 'job_title', 'status',
        'file_path', 'file_name', 'notes', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'salary'     => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Tenant\Employee::class);
    }

    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->end_date) return null;
        return max(0, now()->diffInDays($this->end_date, false));
    }
}
