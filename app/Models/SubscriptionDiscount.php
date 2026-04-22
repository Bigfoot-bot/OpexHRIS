<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionDiscount extends Model
{
    protected $fillable = ['cycle', 'months', 'discount_percentage'];

    protected $casts = ['discount_percentage' => 'decimal:2'];

    public static function getDiscount(string $cycle): float
    {
        $discount = static::where('cycle', $cycle)->first();
        return $discount ? (float) $discount->discount_percentage : 0;
    }

    public static function getMonths(string $cycle): int
    {
        return match($cycle) {
            'monthly'   => 1,
            'quarterly' => 3,
            'biannual'  => 6,
            'annual'    => 12,
            default     => 1,
        };
    }
}
