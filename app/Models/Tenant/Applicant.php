<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'job_position_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'current_employer',
        'current_position',
        'years_of_experience',
        'highest_qualification',
        'resume',
        'cover_letter',
        'stage',
        'score',
        'notes',
        'interview_date',
    ];

    protected $casts = [
        'interview_date' => 'date',
    ];

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStageColorAttribute(): string
    {
        return match($this->stage) {
            'applied'     => 'gray',
            'shortlisted' => 'blue',
            'interview'   => 'amber',
            'assessment'  => 'purple',
            'offer'       => 'teal',
            'hired'       => 'emerald',
            'rejected'    => 'red',
            default       => 'gray',
        };
    }
}