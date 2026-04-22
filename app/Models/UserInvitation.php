<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserInvitation extends Model
{
    protected $fillable = [
        'tenant_id',
        'email',
        'name',
        'token',
        'role',
        'employee_id',
        'accepted_at',
        'expires_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return !$this->isAccepted() && !$this->isExpired();
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public static function createInvitation(
        string $tenantId,
        string $email,
        string $name,
        string $role,
        ?int $employeeId = null
    ): self {
        // Delete any existing pending invitations for this email
        self::where('tenant_id', $tenantId)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        return self::create([
            'tenant_id'   => $tenantId,
            'email'       => $email,
            'name'        => $name,
            'token'       => self::generateToken(),
            'role'        => $role,
            'employee_id' => $employeeId,
            'expires_at'  => now()->addDays(7),
        ]);
    }
}