<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ClearanceItem extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'separation_id', 'department', 'item',
        'status', 'cleared_by', 'cleared_at', 'notes',
    ];
    protected $casts = ['cleared_at' => 'datetime'];
    public function separation() { return $this->belongsTo(Separation::class); }
}
