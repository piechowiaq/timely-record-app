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

        // Trainings
        Permission::create(['name' => 'view training']);
        Permission::create(['name' => 'create training']);
        Permission::create(['name' => 'update training']);
        Permission::create(['name' => 'delete training']);

        // Departments
        Permission::create(['name' => 'view department']);
        Permission::create(['name' => 'create department']);
        Permission::create(['name' => 'update department']);
        Permission::create(['name' => 'delete department']);

        // Positions
        Permission::create(['name' => 'view position']);
        Permission::create(['name' => 'create position']);
        Permission::create(['name' => 'update position']);
        Permission::create(['name' => 'delete position']);

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
                // Training permissions
                'view training',
                'create training',
                'update training',
                'delete training',
                // Department permissions
                'view department',
                'create department',
                'update department',
                'delete department',
                // Position permissions
                'view position',
                'create position',
                'update position',
                'delete position',
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
                // Training permissions
                'view training',
                'create training',
                'update training',
                'delete training',
                // Department permissions
                'view department',
                'create department',
                'update department',
                'delete department',
                // Position permissions
                'view position',
                'create position',
                'update position',
                'delete position',
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

                // Project permissions
                'view project',
                // Workspace permissions
                'view workspace',
                // Registry permissions
                'view registry',
                // Training permissions
                'view training',
                // Department permissions
                'view department',
                // Position permissions
                'view position',
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

                // Project permissions
                'view project',
                // Workspace permissions
                'view workspace',
                // Registry permissions
                'view registry',
                // Training permissions
                'view training',
                // Department permissions
                'view department',
                // Position permissions
                'view position',
                // Report permissions
                'view report',

            ]);
    }
}
