<?php

use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('shows workspace name', function () {

    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    $workspace = Workspace::first();

    get(route('workspaces.dashboard', ['project' => $user->project_id, 'workspace' => $workspace->id]))
        ->assertOk()
        ->assertSee($workspace->name);

});
