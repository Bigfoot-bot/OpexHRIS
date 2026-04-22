<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PerformanceGoal extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'title',
        'description',
        'category',
        'weight',
        'status',
        'progress',
        'target_value',
        'actual_value',
        'measurement_unit',
        'start_date',
        'due_date',
        'completed_date',
        'year',
        'quarter',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'due_date'       => 'date',
        'completed_date' => 'date',
        'target_value'   => 'decimal:2',
        'actual_value'   => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed'   => 'emerald',
            'in_progress' => 'blue',
            'cancelled'   => 'red',
            default       => 'gray',
        };
    }
}