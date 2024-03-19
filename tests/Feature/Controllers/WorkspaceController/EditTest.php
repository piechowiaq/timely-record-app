<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.edit', Workspace::factory()->create()))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('workspaces.edit', Workspace::factory()->create(['project_id' => $user->project_id])))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
    $user->workspaces()->attach($workspace);

    actingAs($user)->
    get(route('workspaces.edit', $workspace->id))
        ->assertComponent('Workspaces/Edit');

});

it('passes correct workspace to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
    $user->workspaces()->attach($workspace);

    actingAs($user)->
    get(route('workspaces.edit', $workspace->id))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace));
});
