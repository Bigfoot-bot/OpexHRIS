<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProfessionalLicense extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'license_name',
        'license_number',
        'issuing_body',
        'issue_date',
        'expiry_date',
        'status',
        'document',
        'notes',
        'alert_sent',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'expiry_date' => 'date',
        'alert_sent'  => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function updateStatus(): void
    {
        $daysUntilExpiry = now()->diffInDays($this->expiry_date, false);

        if ($daysUntilExpiry < 0) {
            $this->status = 'expired';
        } elseif ($daysUntilExpiry <= 90) {
            $this->status = 'expiring';
        } else {
            $this->status = 'valid';
        }

        $this->save();
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'valid'    => 'emerald',
            'expiring' => 'amber',
            'expired'  => 'red',
            default    => 'gray',
        };
    }
}