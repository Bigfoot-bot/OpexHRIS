<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ExitInterview extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'separation_id', 'employee_id',
        'rating_overall', 'rating_management', 'rating_work_environment',
        'rating_compensation', 'rating_growth',
        'reason_leaving', 'what_worked_well', 'what_could_improve',
        'would_recommend', 'would_return', 'additional_comments',
        'is_submitted', 'submitted_at',
    ];
    protected $casts = [
        'would_return' => 'boolean',
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];
    public function separation() { return $this->belongsTo(Separation::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}
