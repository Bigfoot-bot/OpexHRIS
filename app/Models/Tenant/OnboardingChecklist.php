<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class OnboardingChecklist extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'title',
        'description',
        'category',
        'is_completed',
        'completed_by',
        'completed_at',
        'order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'documentation' => 'blue',
            'it_setup'      => 'purple',
            'training'      => 'emerald',
            'introduction'  => 'amber',
            'compliance'    => 'red',
            default         => 'gray',
        };
    }

    // Generate default checklist items for a new employee
    public static function generateDefault(Employee $employee): void
    {
        $items = [
            ['title' => 'Collect signed employment contract', 'category' => 'documentation', 'order' => 1],
            ['title' => 'Collect copy of National ID', 'category' => 'documentation', 'order' => 2],
            ['title' => 'Collect KRA PIN certificate', 'category' => 'documentation', 'order' => 3],
            ['title' => 'Collect NHIF card/number', 'category' => 'documentation', 'order' => 4],
            ['title' => 'Collect NSSF number', 'category' => 'documentation', 'order' => 5],
            ['title' => 'Collect professional license/registration', 'category' => 'compliance', 'order' => 6],
            ['title' => 'Set up email account', 'category' => 'it_setup', 'order' => 7],
            ['title' => 'Set up system access/portal login', 'category' => 'it_setup', 'order' => 8],
            ['title' => 'Provide facility tour', 'category' => 'introduction', 'order' => 9],
            ['title' => 'Introduce to department team', 'category' => 'introduction', 'order' => 10],
            ['title' => 'Introduce to line manager', 'category' => 'introduction', 'order' => 11],
            ['title' => 'Complete infection prevention & control training', 'category' => 'training', 'order' => 12],
            ['title' => 'Complete BLS/fire safety training', 'category' => 'training', 'order' => 13],
            ['title' => 'Review HR policies & procedures', 'category' => 'compliance', 'order' => 14],
            ['title' => 'Complete probation goals setting', 'category' => 'other', 'order' => 15],
        ];

        foreach ($items as $item) {
            self::create([
                'tenant_id'   => $employee->tenant_id,
                'employee_id' => $employee->id,
                'title'       => $item['title'],
                'category'    => $item['category'],
                'order'       => $item['order'],
            ]);
        }
    }
}