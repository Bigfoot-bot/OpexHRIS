<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'department',
        'location',
        'type',
        'status',
        'description',
        'requirements',
        'responsibilities',
        'salary_min',
        'salary_max',
        'vacancies',
        'closing_date',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'salary_min'   => 'decimal:2',
        'salary_max'   => 'decimal:2',
    ];

    public function applicants()
    {
        return $this->hasMany(Applicant::class, 'job_position_id');
    }

    public function getTypeLabellAttribute(): string
    {
        return match($this->type) {
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract'  => 'Contract',
            'intern'    => 'Internship',
            default     => $this->type,
        };
    }
}