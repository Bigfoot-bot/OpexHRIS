<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Branch extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'slug', 'address', 'phone', 'email',
        'manager_id', 'status', 'notes',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($branch) {
            if (!$branch->slug) {
                $branch->slug = Str::slug($branch->name);
            }
        });
    }

    public function manager()
    {
        return $this->belongsTo(\App\Models\Tenant\User::class, 'manager_id');
    }

    public function employees()
    {
        return $this->hasMany(\App\Models\Tenant\Employee::class, 'branch_id');
    }

    public function budgetAllocation()
    {
        return $this->hasOne(BranchBudgetAllocation::class, 'branch_id');
    }

    public function users()
    {
        return \App\Models\Tenant\User::where('branch_id', $this->id);
    }
}
