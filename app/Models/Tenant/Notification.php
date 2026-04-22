<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'success' => 'emerald',
            'warning' => 'amber',
            'danger'  => 'red',
            default   => 'blue',
        };
    }

    public function getTypeBgAttribute(): string
    {
        return match($this->type) {
            'success' => 'bg-emerald-50 text-emerald-700',
            'warning' => 'bg-amber-50 text-amber-700',
            'danger'  => 'bg-red-50 text-red-700',
            default   => 'bg-blue-50 text-blue-700',
        };
    }

    // Static helper to create notifications
    public static function notify(
        string $title,
        string $message,
        string $type = 'info',
        string $link = null
    ): void {
        $users = User::where('tenant_id', tenant('id'))->get();
        foreach ($users as $user) {
            self::create([
                'tenant_id' => tenant('id'),
                'user_id'   => $user->id,
                'title'     => $title,
                'message'   => $message,
                'type'      => $type,
                'link'      => $link,
            ]);
        }
    }
}