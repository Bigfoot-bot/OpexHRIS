<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class IpWhitelist extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'ip_address', 'label', 'is_active', 'created_by'];
    protected $casts = ['is_active' => 'boolean'];
}
