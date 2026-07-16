<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Enums\RoleName;
use App\Enums\PermissionName;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Roles
        foreach (RoleName::cases() as $roleEnum) {
            Role::firstOrCreate(
                ['name' => $roleEnum->value],
                ['display_name' => $roleEnum->label(), 'description' => $roleEnum->label() . ' Role']
            );
        }

        // Seed Permissions
        foreach (PermissionName::cases() as $permissionEnum) {
            Permission::firstOrCreate(
                ['name' => $permissionEnum->value],
                ['description' => 'Permission to ' . str_replace('_', ' ', $permissionEnum->value)]
            );
        }

        // Assign all permissions to SuperAdmin
        $superAdmin = Role::where('name', RoleName::SuperAdmin->value)->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync(Permission::all());
        }

        // Assign relevant admin permissions to Admin role
        $admin = Role::where('name', RoleName::Admin->value)->first();
        if ($admin) {
            $adminPermissions = Permission::whereIn('name', [
                'users.view', 'users.create', 'users.update', 'users.delete', 'users.assign_roles',
                'roles.view', 'roles.manage',
                'brands.manage', 'car_models.manage',
                'cars.view_all', 'cars.create', 'cars.update', 'cars.delete', 'cars.approve',
                'requests.view_all', 'requests.update_status',
                'audit_logs.view',
            ])->get();
            $admin->permissions()->sync($adminPermissions);
        }

        // Assign agent permissions to SalesAgent role
        $agent = Role::where('name', RoleName::SalesAgent->value)->first();
        if ($agent) {
            $agentPermissions = Permission::whereIn('name', [
                'requests.view_all', 'requests.update_status',
            ])->get();
            $agent->permissions()->sync($agentPermissions);
        }
    }
}
