<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ShiftAssignment extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'roster_id', 'employee_id', 'shift_id', 'date',
        'status', 'actual_start', 'actual_end', 'notes', 'assigned_by',
    ];
    protected $casts = [
        'date' => 'date',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function roster()
    {
        return $this->belongsTo(Roster::class);
    }
}
