<?php

namespace App\Traits;

use App\Models\Central\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Automatically set tenant_id on create
        static::creating(function ($model) {
            if (tenancy()->initialized) {
                $model->tenant_id = tenant('id');
            }
        });

        // Automatically scope all queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenancy()->initialized) {
                $builder->where(
                    $builder->getModel()->getTable() . '.tenant_id',
                    tenant('id')
                );
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}