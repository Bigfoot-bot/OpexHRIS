<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'description', 'monthly_price', 'max_employees', 'features', 'is_active', 'is_featured'
    ];

    protected $casts = [
        'features'    => 'array',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'monthly_price' => 'decimal:2',
    ];

    public function calculatePrice(string $cycle, float $discountPercentage): float
    {
        $months = match($cycle) {
            'monthly'   => 1,
            'quarterly' => 3,
            'biannual'  => 6,
            'annual'    => 12,
        };
        $subtotal = $this->monthly_price * $months;
        $discount = $subtotal * ($discountPercentage / 100);
        return $subtotal - $discount;
    }
}
