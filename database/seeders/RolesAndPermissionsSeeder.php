<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions:

        // Users
        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        // Projects
        Permission::create(['name' => 'view project']);
        Permission::create(['name' => 'update project']);

        // Workspaces
        Permission::create(['name' => 'view workspace']);
        Permission::create(['name' => 'create workspace']);
        Permission::create(['name' => 'update workspace']);
        Permission::create(['name' => 'delete workspace']);

        // Create roles and assign created permissions

        // Super Admin
        Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());

        // Project Admin
        Role::create(['name' => 'project-admin'])
            ->givePermissionTo([
                // User permissions
                'view user',
                'create user',
                'update user',
                'delete user',
                // Project permissions
                'view project',
                'update project',
                // Workspace permissions
                'view workspace',
                'create workspace',
                'update workspace',
                'delete workspace',
            ]);

        // Admin
        Role::create(['name' => 'admin'])
            ->givePermissionTo([
                // User permissions
                'view user',
                'create user',
                'update user',
                'delete user',
                // Project permissions
                'view project',
                'update project',
                // Workspace permissions
                'view workspace',
                'create workspace',
                'update workspace',
            ]);

        // Manager
        Role::create(['name' => 'manager'])
            ->givePermissionTo([
                // User permissions
                'view user',
                'create user',
                'update user',
                'delete user',
                // Project permissions
                'view project',
                // Workspace permissions
                'view workspace',
                'create workspace',
                'update workspace',
                'delete workspace',
            ]);

        // User
        Role::create(['name' => 'user'])
            ->givePermissionTo([
                // User permissions
                'view user',
                'update user',
                // Project permissions

                // Workspace permissions
                'view workspace',
            ]);
    }
}
