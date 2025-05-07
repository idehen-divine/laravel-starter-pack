<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create Permissions
        $permissions = PermissionEnum::cases();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $roles = RoleEnum::cases();
        
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $ownerRole = Role::findByName(RoleEnum::OWNER->name);
        $adminRole = Role::findByName(RoleEnum::ADMIN->name);
        $userRole = Role::findByName(RoleEnum::USER->name);

        // Assign Permissions to Roles
        $ownerRole->givePermissionTo(Permission::all());
        $adminRole->givePermissionTo([
            PermissionEnum::ACCESS_ADMIN_PANEL->name,
            PermissionEnum::VIEW_USERS->name,
            PermissionEnum::VIEW_ROLES->name,
            PermissionEnum::EDIT_ROLES->name,
            PermissionEnum::VIEW_PERMISSION->name,
            PermissionEnum::EDIT_PERMISSION->name,
            PermissionEnum::VIEW_SETTINGS->name,
            PermissionEnum::EDIT_SETTINGS->name,
            PermissionEnum::VIEW_LOGS->name,
        ]);
        $userRole->givePermissionTo([
            PermissionEnum::ACCESS_USER->name,
        ]);
    }
}
