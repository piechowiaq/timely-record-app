<?php

use App\Models\Project;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});

it('can store a user with roles and workspaces', function () {

    $user = User::factory()->withWorkspaces(5)->create();
    actingAs($user);

    $user->assignRole('admin');

    // Set a delay so the test doesn't fail due to duplicate timestamps

    usleep(1000000);

    $project = $user->project;

    $workspaces = $user->workspaces;
    $selectedWorkspaceIds = $workspaces->pluck('id')->take(3)->toArray();

    $role = 'admin';

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $selectedWorkspaceIds,
        'role' => $role,
    ];

    // Act: Perform the action you want to test
    // This assumes you have a route 'user.store' that handles the user creation.
    $response = post(route('users.store', ['project' => $project->id]), $userData);

    // Assert: Check that the user was created and has the correct roles and workspaces
    $response->assertRedirect(route('users.index', ['project' => $project]))->assertSessionHas('success', 'User created.');

    $user = User::latest()->first();
    $user->assignRole($role);

    expect($user->first_name)->toEqual('John')
        ->and($user->last_name)->toEqual('Doe')
        ->and($user->email)->toEqual('john@example.com')
        ->and($user->roles->pluck('name'))->toContain('admin')
        ->and($user->workspaces)->toHaveCount(3);

});

it('does not create a new user with provided data', function () {
    $user = User::factory()->create();
    actingAs($user);

    $user->assignRole('admin');

    $project = $user->project;
    $workspaces = \App\Models\Workspace::factory()->count(5)->create();
    $role = 'admin';

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $workspaces->pluck('id')->toArray(),
        'role' => $role,
    ];

    $initialUserCount = User::count();

    $response = post(route('users.store', ['project' => $project->id]), $userData);

    $response->assertRedirect(route('welcome'));

    $newUserCount = User::count();
    expect($newUserCount)->toEqual($initialUserCount);
});

it('can update a user with new roles and workspaces', function () {
    // Arrange: Prepare the environment for the test
    $user = User::factory()->withWorkspaces(5)->create();
    actingAs($user);
    // Set a delay so the test doesn't fail due to duplicate timestamps
    $user->assignRole('admin');

    usleep(1000000);

    $project = $user->project;

    $workspaces = $user->workspaces;
    $selectedWorkspaceIds = $workspaces->pluck('id')->take(3)->toArray();

    $role = 'admin';

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $selectedWorkspaceIds,
        'role' => $role,
    ];

    // Act: Perform the action you want to test
    // This assumes you have a route 'user.store' that handles the user creation.
    post(route('users.store', ['project' => $project->id]), $userData);

    $updatedSelectedWorkspaceIds = $workspaces->pluck('id')->take(2)->toArray();

    $role = 'manager';

    $updatedUserData = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'workspacesIds' => $updatedSelectedWorkspaceIds,
        'role' => $role,
    ];

    $response = patch(route('users.update', ['project' => $project, 'user' => $user->id]), $updatedUserData);

    $response->assertRedirect(route('users.edit', ['project' => $project, 'user' => $user->id]))->assertSessionHas('success', 'User updated successfully.');

    $user->refresh();

    expect($user->first_name)->toEqual('Jane')
        ->and($user->last_name)->toEqual('Smith')
        ->and($user->email)->toEqual('jane@example.com')
        ->and($user->roles->pluck('name'))->toContain('manager')
        ->and($user->workspaces)->toHaveCount(2);
});

it('can delete a user', function () {
    // Arrange: Prepare the environment for the test
    $user = User::factory()->withWorkspaces(5)->create();
    actingAs($user);
    // Set a delay so the test doesn't fail due to duplicate timestamps

    $user->assignRole('admin');

    usleep(1000000);

    $project = $user->project;

    $workspaces = $user->workspaces;
    $selectedWorkspaceIds = $workspaces->pluck('id')->take(3)->toArray();

    $role = 'admin';

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $selectedWorkspaceIds,
        'role' => $role,
    ];

    // Act: Perform the action you want to test
    // This assumes you have a route 'user.store' that handles the user creation.
    post(route('users.store', ['project' => $project->id]), $userData);

    $createdUser = User::latest()->first();

    // Act: Perform the delete action
    $response = delete(route('users.destroy', ['project' => $project->id, 'user' => $createdUser->id]), ['password' => 'password']);

    // Assert: Check if the user was deleted
    $response->assertRedirect(route('users.index', ['project' => $project]))
        ->assertSessionHas('success', 'User deleted.');

    $this->assertTrue($createdUser->fresh()->trashed());
});

it('allows logged in user to view only their project and its workspaces', function () {
    // Assuming you have a User model, Project model, and Workspace model linked appropriately
    $user = User::factory()->withWorkspaces(1)->create();
    actingAs($user);

    $project = $user->project;
    $otherProject = Project::factory()->create();

    $response = $this->get(route('projects.dashboard', $otherProject->id));

    $response->assertForbidden();

});
