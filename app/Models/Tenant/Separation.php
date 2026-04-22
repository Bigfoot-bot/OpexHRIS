<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Separation extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'employee_id', 'type', 'notice_date', 'last_working_date',
        'effective_date', 'status', 'reason', 'leave_encashment', 'gratuity',
        'pending_claims', 'loan_balance', 'final_dues', 'certificate_issued',
        'certificate_date', 'approved_by', 'notes',
    ];
    protected $casts = [
        'notice_date'       => 'date',
        'last_working_date' => 'date',
        'effective_date'    => 'date',
        'certificate_date'  => 'date',
        'certificate_issued'=> 'boolean',
    ];
    public function employee() { return $this->belongsTo(Employee::class); }
    public function clearanceItems() { return $this->hasMany(ClearanceItem::class); }
    public function exitInterview() { return $this->hasOne(ExitInterview::class); }
}
