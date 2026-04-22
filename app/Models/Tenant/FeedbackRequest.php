<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class FeedbackRequest extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'employee_id', 'requested_by', 'title', 'description',
        'type', 'due_date', 'status', 'total_reviewers', 'completed_reviews',
    ];
    protected $casts = ['due_date' => 'date'];
    public function employee() { return $this->belongsTo(Employee::class); }
    public function responses() { return $this->hasMany(FeedbackResponse::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
}
