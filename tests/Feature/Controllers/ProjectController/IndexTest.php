<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('requires authentication', function () {

    get(route('projects.dashboard', Project::factory()->create()))
        ->assertRedirect(route('login'));

});

it('returns a correct component', function () {

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)->
    get(route('projects.dashboard', [$user->project_id]))
        ->assertComponent('Projects/Dashboard');

});

it('passes project workspaces to the view', function () {

    $user = User::factory()->withWorkspaces(5)->create();
    $user->assignRole('admin');

    actingAs($user)->
    get(route('projects.dashboard', [$user->project_id]))
        ->assertHasResource('workspaces', WorkspaceResource::collection($user->project->workspaces));

});
