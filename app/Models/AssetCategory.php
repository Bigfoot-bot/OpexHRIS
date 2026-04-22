<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'color', 'has_number_plate'];

    protected $casts = ['has_number_plate' => 'boolean'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'asset_category_id');
    }
}
