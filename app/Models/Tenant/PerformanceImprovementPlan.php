<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PerformanceImprovementPlan extends Model
{
    use BelongsToTenant;
    protected $table = 'performance_improvement_plans';
    protected $fillable = [
        'tenant_id', 'employee_id', 'created_by', 'title', 'reason',
        'goals', 'support_provided', 'start_date', 'end_date', 'status',
        'progress_notes', 'outcome', 'review_date', 'reviewed_by',
    ];
    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'review_date' => 'date',
    ];
    public function employee() { return $this->belongsTo(Employee::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
