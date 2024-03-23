<?php

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
