<?php

use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Radisson Blu',
        'location' => 'Warszawa',
    ];
});

it('requires authentication', function () {

    put(route('workspaces.update', Workspace::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->put(route('workspaces.update', Workspace::factory()->create(['project_id' => $user->project_id])->id))
            ->assertForbidden();
    }

});

it('updates a workspace', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
    $user->workspaces()->attach($workspace);

    $workspaceData = value($this->validData);

    actingAs($user)->put(route('workspaces.update', $workspace->id), $workspaceData);

    $this->assertDatabaseHas(Workspace::class, [
        ...$workspaceData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the workspace edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
    $user->workspaces()->attach($workspace);

    $workspaceData = value($this->validData);

    actingAs($user)->put(route('workspaces.update', $workspace->id), $workspaceData)
        ->assertRedirect(route('workspaces.edit', $workspace->id));
});
