<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('requires authentication', function () {

    get(route('projects.dashboard'))
        ->assertRedirect(route('login'));

});

it('returns a correct component', function () {

    $user = User::role('project-admin')->first();
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('projects.dashboard'))
        ->assertComponent('Projects/Dashboard');

});

it('passes project workspaces to the view', function () {

    $user = User::role('project-admin')->first();
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('projects.dashboard'))
        ->assertHasResource('workspaces', WorkspaceResource::collection($user->workspaces));

});
