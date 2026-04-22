<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'employee_id', 'week_start', 'week_end',
        'total_hours', 'regular_hours', 'overtime_hours',
        'status', 'notes', 'approved_by', 'approved_at',
    ];
    protected $casts = [
        'week_start'  => 'date',
        'week_end'    => 'date',
        'approved_at' => 'datetime',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function entries()
    {
        return $this->hasMany(TimesheetEntry::class);
    }
}
