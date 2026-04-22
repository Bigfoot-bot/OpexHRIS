<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $fillable = [
        'tenant_id', 'asset_id', 'employee_id', 'assigned_date',
        'return_date', 'status', 'notes', 'assigned_by',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'return_date'   => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Tenant\Employee::class);
    }
}
