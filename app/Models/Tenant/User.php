<?php
namespace App\Models\Tenant;
use App\Traits\BelongsToTenant;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use BelongsToTenant, SoftDeletes, HasRoles;
    protected $table = 'tenant_users';
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'employee_id',
        'branch_id',
        'status',
        'portal_preference',
        'is_hr',
        'is_admin',
        'last_login_at',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'last_login_at' => 'datetime',
        'is_hr'         => 'boolean',
        'is_admin'      => 'boolean',
    ];
    public function tenantRoles()
    {
        return $this->hasMany(\App\Models\TenantUserRole::class, 'user_id');
    }
    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) return true;
        if (!isset($this->_permissionsCache)) {
            $this->_permissionsCache = $this->tenantRoles()
                ->with('role.permissions')
                ->get()
                ->flatMap(fn($ur) => $ur->role->permissions->pluck('permission'))
                ->toArray();
        }
        return in_array($permission, $this->_permissionsCache);
    }
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function isHR(): bool
    {
        return $this->is_hr === true;
    }
    public function isDeptHead(): bool
    {
        return $this->hasRole('Department Head');
    }
    public function isEmployee(): bool
    {
        return !$this->is_hr;
    }
    public function canSwitchPortal(): bool
    {
        if ($this->is_admin) return true;
        return ($this->is_hr || $this->tenantRoles()->count() > 0) && $this->employee_id !== null;
    }
    public function isInEmployeePortal(): bool
    {
        return $this->portal_preference === 'employee';
    }
    public function isInHRPortal(): bool
    {
        return $this->portal_preference === 'hr';
    }
}


