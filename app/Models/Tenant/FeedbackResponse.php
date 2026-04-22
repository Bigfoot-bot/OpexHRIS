<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class FeedbackResponse extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'feedback_request_id', 'reviewer_id',
        'rating_overall', 'rating_communication', 'rating_teamwork',
        'rating_technical', 'rating_leadership',
        'strengths', 'improvements', 'comments',
        'is_anonymous', 'is_submitted', 'submitted_at',
    ];
    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_submitted'  => 'boolean',
        'submitted_at'  => 'datetime',
    ];
    public function request() { return $this->belongsTo(FeedbackRequest::class, 'feedback_request_id'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
}
