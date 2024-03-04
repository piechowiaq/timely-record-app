<?php

use App\Models\User;
use App\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

it('shows workspace name', function () {

    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    $user->assignRole('user');
    $workspace = Workspace::first();

    $response = get(route('workspaces.dashboard', ['project' => $user->project_id, 'workspace' => $workspace->id]));

    expect($response->status())->toBe(200);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Workspaces/Dashboard')
        ->where('workspace.name', $workspace->name));

});
