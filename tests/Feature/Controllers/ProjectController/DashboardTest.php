<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('projects.dashboard'))
        ->assertRedirect(route('login'));

});
it('requires authorization', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');
    session(['project_id' => $user->project_id]);

    actingAs($user)
        ->get(route('projects.dashboard'))
        ->assertRedirect(route('workspaces.dashboard', $user->workspaces->first()->id));
});

it('returns a correct component', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('projects.dashboard'))
        ->assertComponent('Projects/Dashboard');

});

it('passes auth user workspaces to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $workspaces = $user->workspaces;
    $workspaces->each(function ($workspace) {
        $workspace->registryMetrics = 0;
    });

    actingAs($user)->
    get(route('projects.dashboard'))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($workspaces));

});
