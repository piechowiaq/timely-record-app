<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.dashboard', Workspace::factory()->create()))
        ->assertRedirect(route('login'));

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');

    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.dashboard', $user->workspaces->first()->id))
        ->assertComponent('Workspaces/Dashboard');

});

it('passes auth user workspaces to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('projects.dashboard'))
        ->assertHasResource('workspaces', WorkspaceResource::collection($user->workspaces));

});
