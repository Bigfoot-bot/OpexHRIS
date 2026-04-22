<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantPermission extends Model
{
    protected $fillable = ['tenant_id', 'role_id', 'permission'];

    public function role()
    {
        return $this->belongsTo(TenantRole::class, 'role_id');
    }
}
