<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('shows workspaces names', function () {

    $user = User::factory()->withWorkspaces(4)->create();
    actingAs($user);

    $workspaces = $user->project->workspaces();

    get(route('projects.dashboard', ['project' => $user->project_id]))
        ->assertOk()
        ->assertSeeInOrder($workspaces->pluck('name')->all());
    //        ->assertInertia(fn (Assert $page) => $page
    //            ->has('workspaces', 4));

});
