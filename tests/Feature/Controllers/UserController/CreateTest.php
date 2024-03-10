<?php

use App\Http\Resources\RoleResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('users.create'))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('users.create', $user->project_id))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)->
    get(route('users.create', [$user->project_id]))
        ->assertComponent('Users/Create');

});

it('passes auth user workspaces to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();

    actingAs($user)->
    get(route('users.create', [$user->project_id]))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($user->workspaces));

});

it('passes eligible roles to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();

    $roles = Role::whereNotIn('name', ['project-admin', 'super-admin', 'admin'])->get();

    actingAs($user)->
    get(route('users.create', [$user->project_id]))
        ->assertHasResource('roles', RoleResource::collection($roles));

});

it('passes user workspaces ids to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();

    actingAs($user)->
    get(route('users.create', [$user->project_id]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('workspacesIds', $user->workspaces->pluck('id')->count())
        );

});
