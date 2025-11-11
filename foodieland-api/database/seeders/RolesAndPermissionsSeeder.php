<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'manage recipes']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'delete comments']);

        // create roles and assign created permissions
        $userRole = Role::create(['name' => 'User']);
        // User role gets no special permissions by default

        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
