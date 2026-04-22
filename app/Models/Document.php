<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'tenant_id', 'category_id', 'title', 'description', 'file_path',
        'file_name', 'file_type', 'file_size', 'visibility',
        'requires_acknowledgment', 'is_template', 'uploaded_by',
    ];

    protected $casts = [
        'requires_acknowledgment' => 'boolean',
        'is_template'             => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function acknowledgments()
    {
        return $this->hasMany(DocumentAcknowledgment::class);
    }

    public function isAcknowledgedBy(int $employeeId): bool
    {
        return $this->acknowledgments()->where('employee_id', $employeeId)->whereNotNull('acknowledged_at')->exists();
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        $bytes = $this->file_size;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
