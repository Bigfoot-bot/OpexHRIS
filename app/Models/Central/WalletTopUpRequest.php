<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class WalletTopUpRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'payment_method',
        'amount',
        'transaction_reference',
        'mpesa_phone',
        'bank_name',
        'bank_reference',
        'proof_file',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'daraja_checkout_request_id',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
