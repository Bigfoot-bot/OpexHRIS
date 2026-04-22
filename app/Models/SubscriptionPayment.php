<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'tenant_id', 'plan_id', 'invoice_id', 'cycle', 'payment_method',
        'amount', 'vat_amount', 'discount_amount', 'transaction_reference',
        'mpesa_phone', 'bank_name', 'bank_reference', 'proof_file',
        'daraja_checkout_request_id', 'status', 'rejection_reason',
        'approved_by', 'approved_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'vat_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'approved_at'     => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function invoice()
    {
        return $this->belongsTo(SubscriptionInvoice::class);
    }

    public static function isTransactionCodeUsed(string $code): bool
    {
        // Cross-check against ALL reference fields in subscription payments
        $inSubMpesa = static::where('transaction_reference', $code)
                            ->whereIn('status', ['pending', 'approved'])->exists();
        $inSubBank  = static::where('bank_reference', $code)
                            ->whereIn('status', ['pending', 'approved'])->exists();

        // Cross-check against ALL reference fields in wallet top-ups
        $inWalletMpesa = \DB::table('wallet_top_up_requests')
                            ->where('transaction_reference', $code)
                            ->whereIn('status', ['pending', 'approved'])->exists();
        $inWalletBank  = \DB::table('wallet_top_up_requests')
                            ->where('bank_reference', $code)
                            ->whereIn('status', ['pending', 'approved'])->exists();

        return $inSubMpesa || $inSubBank || $inWalletMpesa || $inWalletBank;
    }

    public static function isBankReferenceUsed(string $ref): bool
    {
        // Cross-check against ALL reference fields in subscription payments
        $inSubMpesa = static::where('transaction_reference', $ref)
                            ->whereIn('status', ['pending', 'approved'])->exists();
        $inSubBank  = static::where('bank_reference', $ref)
                            ->whereIn('status', ['pending', 'approved'])->exists();

        // Cross-check against ALL reference fields in wallet top-ups
        $inWalletMpesa = \DB::table('wallet_top_up_requests')
                            ->where('transaction_reference', $ref)
                            ->whereIn('status', ['pending', 'approved'])->exists();
        $inWalletBank  = \DB::table('wallet_top_up_requests')
                            ->where('bank_reference', $ref)
                            ->whereIn('status', ['pending', 'approved'])->exists();

        return $inSubMpesa || $inSubBank || $inWalletMpesa || $inWalletBank;
    }
}
