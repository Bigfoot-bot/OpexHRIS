<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'reviewer_id',
        'review_period',
        'review_year',
        'review_type',
        'status',
        'self_rating',
        'manager_rating',
        'final_rating',
        'self_assessment',
        'manager_comments',
        'strengths',
        'areas_for_improvement',
        'goals_next_period',
        'review_date',
        'due_date',
    ];

    protected $casts = [
        'review_date' => 'date',
        'due_date'    => 'date',
        'self_rating'    => 'decimal:1',
        'manager_rating' => 'decimal:1',
        'final_rating'   => 'decimal:1',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getRatingLabelAttribute(): string
    {
        $rating = $this->final_rating ?? $this->manager_rating ?? $this->self_rating;
        if (!$rating) return 'Not Rated';
        if ($rating >= 4.5) return 'Outstanding';
        if ($rating >= 3.5) return 'Exceeds Expectations';
        if ($rating >= 2.5) return 'Meets Expectations';
        if ($rating >= 1.5) return 'Needs Improvement';
        return 'Unsatisfactory';
    }

    public function getRatingColorAttribute(): string
    {
        $rating = $this->final_rating ?? $this->manager_rating ?? $this->self_rating;
        if (!$rating) return 'gray';
        if ($rating >= 4.5) return 'emerald';
        if ($rating >= 3.5) return 'blue';
        if ($rating >= 2.5) return 'amber';
        return 'red';
    }
}