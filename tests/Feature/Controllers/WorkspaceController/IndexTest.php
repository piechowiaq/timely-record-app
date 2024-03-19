<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.index'))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('workspaces.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.index'))
        ->assertComponent('Workspaces/Index');

});

it('passes workspaces to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces(2)->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Workspace::factory(2)->create();

    actingAs($user)->
    get(route('workspaces.index'))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($user->workspaces));

});
