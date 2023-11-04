<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('shows workspaces names and locations', function () {

    $user = User::factory()->withWorkspaces(4)->create();
    actingAs($user);

    $workspaces = $user->project->workspaces;

    $response = get(route('projects.dashboard', ['project' => $user->project_id]));

    expect($response->status())->toBe(200);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Projects/Dashboard')
        ->has('workspaces', 4)
        ->where('workspaces.0.name', $workspaces[0]['name'])
        ->where('workspaces.0.location', $workspaces[0]['location'])
        ->where('workspaces.1.name', $workspaces[1]['name'])
        ->where('workspaces.1.location', $workspaces[1]['location'])
        ->where('workspaces.2.name', $workspaces[2]['name'])
        ->where('workspaces.2.location', $workspaces[2]['location'])
        ->where('workspaces.3.name', $workspaces[3]['name']));

});
