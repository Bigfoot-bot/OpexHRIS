<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantUserRole extends Model
{
    protected $fillable = ['tenant_id', 'user_id', 'role_id'];

    public function role()
    {
        return $this->belongsTo(TenantRole::class, 'role_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Tenant\User::class, 'user_id');
    }
}
