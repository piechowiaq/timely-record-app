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

        // Registries
        Permission::create(['name' => 'view registry']);
        Permission::create(['name' => 'create registry']);
        Permission::create(['name' => 'update registry']);
        Permission::create(['name' => 'delete registry']);

        // Reports
        Permission::create(['name' => 'view report']);
        Permission::create(['name' => 'create report']);
        Permission::create(['name' => 'update report']);
        Permission::create(['name' => 'delete report']);

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
                // Registry permissions
                'view registry',
                'create registry',
                'update registry',
                'delete registry',
                // Report permissions
                'view report',
                'create report',
                'update report',
                'delete report',
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
                // Registry permissions
                'view registry',
                'create registry',
                'update registry',
                'delete registry',
                // Report permissions
                'view report',
                'create report',
                'update report',
                'delete report',
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
                // Registry permissions
                'view registry',
                // Report permissions
                'view report',
                'create report',
                'update report',
                'delete report',
            ]);

        // User
        Role::create(['name' => 'user'])
            ->givePermissionTo([
                // User permissions
                'view user',
                'update user',
                // Project permissions
                'view project',
                // Workspace permissions
                'view workspace',
                // Registry permissions
                'view registry',
                // Report permissions
                'view report',

            ]);
    }
}
