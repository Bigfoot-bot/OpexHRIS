<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryCase extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'case_number',
        'title',
        'description',
        'type',
        'status',
        'severity',
        'incident_date',
        'hearing_date',
        'resolution_date',
        'outcome',
        'employee_response',
        'reported_by',
    ];

    protected $casts = [
        'incident_date'   => 'date',
        'hearing_date'    => 'date',
        'resolution_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'verbal_warning'  => 'blue',
            'written_warning' => 'amber',
            'final_warning'   => 'orange',
            'suspension'      => 'red',
            'termination'     => 'red',
            default           => 'gray',
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'minor'          => 'emerald',
            'moderate'       => 'amber',
            'serious'        => 'orange',
            'gross_misconduct'=> 'red',
            default          => 'gray',
        };
    }
}