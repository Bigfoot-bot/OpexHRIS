<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    protected $fillable = ['tenant_id', 'name', 'description', 'color'];

    public function documents()
    {
        return $this->hasMany(Document::class, 'category_id');
    }
}
