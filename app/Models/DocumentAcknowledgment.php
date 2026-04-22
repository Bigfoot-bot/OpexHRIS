<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAcknowledgment extends Model
{
    protected $fillable = ['tenant_id', 'document_id', 'employee_id', 'acknowledged_at', 'ip_address'];

    protected $casts = ['acknowledged_at' => 'datetime'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Tenant\Employee::class);
    }
}
