<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class FacilityWallet extends Model
{
    protected $fillable = [
        'tenant_id',
        'balance',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'balance'   => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function getOrCreate(string $tenantId): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            ['balance' => 0, 'currency' => 'KES', 'is_active' => true]
        );
    }

    public function credit(float $amount, string $description, string $source, string $reference = null, string $createdBy = null): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        $this->refresh();

        return WalletTransaction::create([
            'tenant_id'      => $this->tenant_id,
            'type'           => 'credit',
            'amount'         => $amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $this->balance,
            'description'    => $description,
            'reference'      => $reference,
            'source'         => $source,
            'created_by'     => $createdBy,
        ]);
    }

    public function debit(float $amount, string $description, string $source, string $reference = null, string $createdBy = null): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        $this->refresh();

        return WalletTransaction::create([
            'tenant_id'      => $this->tenant_id,
            'type'           => 'debit',
            'amount'         => $amount,
            'balance_before' => $balanceBefore,
            'balance_after'  => $this->balance,
            'description'    => $description,
            'reference'      => $reference,
            'source'         => $source,
            'created_by'     => $createdBy,
        ]);
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
