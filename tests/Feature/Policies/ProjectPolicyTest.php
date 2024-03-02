<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows a user with role user that has more then one workspace to view their project dashboard', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces(2)->create();

    $user->assignRole('user');

    actingAs($user)
        ->get(route('projects.dashboard', $user->project))
        ->assertOk();
});

it('forbid user with role user that has only one workspace to view their project dashboard', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces(1)->create();

    $user->assignRole('user');

    actingAs($user);

    $response = get(route('projects.dashboard', ['project' => $user->project]));

    $response->assertRedirect(route('workspaces.dashboard', ['project' => $user->project, 'workspace' => $user->workspaces->first()->id]));

});

it('does not allow a user to view another dashboard', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($user)
        ->get(route('projects.dashboard', $otherUser->project))
        ->assertForbidden();
});
