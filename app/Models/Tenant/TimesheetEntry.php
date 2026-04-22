<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TimesheetEntry extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'timesheet_id', 'date', 'clock_in', 'clock_out',
        'hours', 'project', 'description', 'work_type',
    ];
    protected $casts = ['date' => 'date'];
}
