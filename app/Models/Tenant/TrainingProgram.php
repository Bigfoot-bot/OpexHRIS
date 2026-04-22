<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'type',
        'category',
        'provider',
        'cpd_points',
        'cost',
        'start_date',
        'end_date',
        'location',
        'max_participants',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'cost'       => 'decimal:2',
    ];

    public function enrollments()
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planned'   => 'blue',
            'ongoing'   => 'amber',
            'completed' => 'emerald',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }
}