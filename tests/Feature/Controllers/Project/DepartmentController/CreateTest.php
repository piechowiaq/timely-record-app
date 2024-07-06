<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('departments.create'))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('departments.create'))
            ->assertForbidden();
    }

});

it('returns a correct component for super-admin', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    actingAs($user)->
    get(route('departments.create'))
        ->assertComponent('Projects/Departments/Create');

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)->
    get(route('departments.create'))
        ->assertComponent('Projects/Departments/Create');

});

it('passes auth user workspaces to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();

    actingAs($user)->
    get(route('departments.create'))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($user->workspaces));

});
