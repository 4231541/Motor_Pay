<?php

namespace App\Models\Concerns;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(RoleName|string $role): bool
    {
        $name = $role instanceof RoleName ? $role->value : $role;

        $this->loadMissing('roles');

        return $this->roles->contains(fn (Role $assigned): bool => $assigned->name === $name);
    }

    public function hasAnyRole(RoleName|string ...$roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Super admins implicitly hold every permission; everyone else is checked
     * against the permissions granted through their roles.
     */
    public function hasPermission(PermissionName|string $permission): bool
    {
        if ($this->hasRole(RoleName::SuperAdmin)) {
            return true;
        }

        $name = $permission instanceof PermissionName ? $permission->value : $permission;

        $this->loadMissing('roles.permissions');

        return $this->roles
            ->flatMap(fn (Role $role) => $role->permissions)
            ->contains(fn (Permission $granted): bool => $granted->name === $name);
    }

    public function hasAnyPermission(PermissionName|string|array ...$permissions): bool
    {
        // Flatten: supports both spread args and a single array arg
        $flat = [];
        foreach ($permissions as $p) {
            if (is_array($p)) {
                array_push($flat, ...$p);
            } else {
                $flat[] = $p;
            }
        }

        foreach ($flat as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(RoleName|string $role): void
    {
        $name = $role instanceof RoleName ? $role->value : $role;

        $roleModel = Role::query()->where('name', $name)->firstOrFail();

        $this->roles()->syncWithoutDetaching($roleModel);
        $this->unsetRelation('roles');
    }

    public function removeRole(RoleName|string $role): void
    {
        $name = $role instanceof RoleName ? $role->value : $role;

        $roleModel = Role::query()->where('name', $name)->first();

        if ($roleModel !== null) {
            $this->roles()->detach($roleModel);
            $this->unsetRelation('roles');
        }
    }
}
