<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TrainingEnrollment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'training_program_id',
        'employee_id',
        'status',
        'cpd_points_earned',
        'score',
        'certificate_issued',
        'completion_date',
        'notes',
    ];

    protected $casts = [
        'completion_date'    => 'date',
        'certificate_issued' => 'boolean',
        'score'              => 'decimal:2',
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}