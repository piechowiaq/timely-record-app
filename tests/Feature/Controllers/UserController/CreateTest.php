<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('requires authentication', function () {

    $user = User::factory()->create();

    get(route('users.create'))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);
        session(['project_id' => $user->project_id]);

        actingAs($user)
            ->get(route('users.create', $user->project_id))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)->
    get(route('users.create', [$user->project_id]))
        ->assertComponent('Users/Create');

});

it('passes project workspaces to the view', function () {

    $user = User::factory()->withWorkspaces(5)->create();
    $user->assignRole('admin');

    actingAs($user)->
    get(route('users.create', [$user->project_id]))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($user->project->workspaces));

});
