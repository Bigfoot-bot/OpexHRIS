<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ScheduledReport extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'report_type', 'frequency', 'day_of_week',
        'day_of_month', 'send_time', 'format', 'recipients', 'filters',
        'is_active', 'last_sent_at', 'next_send_at', 'created_by',
    ];
    protected $casts = [
        'filters'      => 'array',
        'is_active'    => 'boolean',
        'last_sent_at' => 'datetime',
        'next_send_at' => 'datetime',
    ];
    public function getRecipientsArrayAttribute(): array
    {
        return array_map('trim', explode(',', $this->recipients));
    }
}
