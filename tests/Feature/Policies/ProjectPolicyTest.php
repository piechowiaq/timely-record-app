<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

it('allows a user with role user that has more then one workspace to view their project dashboard', function () {

    $user = User::factory()->withWorkspaces(2)->create();

    $user->assignRole('user');
    session(['project_id' => $user->project_id]);

    actingAs($user)
        ->get(route('projects.dashboard'))
        ->assertOk();
});

it('forbid user with role user that has only one workspace to view their project dashboard', function () {

    $user = User::factory()->withWorkspaces(1)->create();

    $user->assignRole('user');

    actingAs($user);

    $response = get(route('projects.dashboard'));

    $response->assertRedirect(route('workspaces.dashboard', $user->workspaces->first()->id));

});

it('does not allow a user to view another dashboard', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $user->assignRole('user');

    actingAs($user)
        ->get(route('projects.dashboard', $otherUser->project))
        ->assertForbidden();
});
