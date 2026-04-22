<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OvertimeRequest extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'employee_id', 'date', 'start_time', 'end_time',
        'hours', 'reason', 'status', 'rate_multiplier', 'amount',
        'approved_by', 'approved_at', 'remarks',
    ];
    protected $casts = [
        'date'        => 'date',
        'approved_at' => 'datetime',
        'hours'       => 'decimal:2',
        'amount'      => 'decimal:2',
    ];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function approver()
    {
        return $this->belongsTo(\App\Models\Tenant\User::class, 'approved_by');
    }
}
