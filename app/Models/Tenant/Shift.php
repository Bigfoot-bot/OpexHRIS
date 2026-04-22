<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'code', 'start_time', 'end_time',
        'duration_hours', 'is_night_shift', 'night_shift_allowance',
        'break_duration_minutes', 'color', 'is_active', 'notes',
    ];
    protected $casts = [
        'is_night_shift' => 'boolean',
        'is_active'      => 'boolean',
        'night_shift_allowance' => 'decimal:2',
    ];
    public function assignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}
