<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'tenant_id', 'asset_category_id', 'asset_code', 'name', 'category', 'description',
        'serial_number', 'number_plate', 'brand', 'model', 'purchase_price', 'purchase_date',
        'current_value', 'status', 'location', 'notes',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'purchase_price' => 'decimal:2',
        'current_value'  => 'decimal:2',
    ];

    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)->where('status', 'active')->latest();
    }
}
