<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'department', 'start_date', 'end_date', 'status', 'created_by', 'notes',
    ];
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
    public function assignments()
    {
        return $this->hasMany(ShiftAssignment::class);
    }
}
