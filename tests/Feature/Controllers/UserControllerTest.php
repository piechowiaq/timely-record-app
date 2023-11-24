<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

it('can store a user with roles and workspaces', function () {

    $user = User::factory()->withWorkspaces(5)->create();
    actingAs($user);

    // Set a delay so the test doesn't fail due to duplicate timestamps

    usleep(1000000);

    $project = $user->project;

    $workspaces = $user->workspaces;
    $selectedWorkspaceIds = $workspaces->pluck('id')->take(3)->toArray();

    $role = Role::create(['name' => 'admin']);

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $selectedWorkspaceIds,
        'role' => $role->name,
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

    $project = $user->project;
    $workspaces = \App\Models\Workspace::factory()->count(5)->create();
    $role = Role::create(['name' => 'admin']);

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $workspaces->pluck('id')->toArray(),
        'role' => $role->name,
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

    usleep(1000000);

    $project = $user->project;

    $workspaces = $user->workspaces;
    $selectedWorkspaceIds = $workspaces->pluck('id')->take(3)->toArray();

    $role = Role::create(['name' => 'admin']);

    $userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'workspacesIds' => $selectedWorkspaceIds,
        'role' => $role->name,
    ];

    // Act: Perform the action you want to test
    // This assumes you have a route 'user.store' that handles the user creation.
    post(route('users.store', ['project' => $project->id]), $userData);

    $updatedSelectedWorkspaceIds = $workspaces->pluck('id')->take(2)->toArray();

    $role = Role::create(['name' => 'manager']);

    $updatedUserData = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'workspacesIds' => $updatedSelectedWorkspaceIds,
        'role' => $role->name,
    ];

    $response = patch(route('users.update', ['project' => $project, 'user' => $user->id]), $updatedUserData);
    //
    //    // Assert: Check that the user was updated with the new data
    //    $response->assertRedirect(route('users.edit', ['project' => $project, 'user' => $user->id]))
    //        ->assertSessionHas('success', 'User updated.');

    $response->assertRedirect(route('users.edit', ['project' => $project, 'user' => $user->id]))->assertSessionHas('success', 'User updated successfully.');

    $user->refresh();

    expect($user->first_name)->toEqual('Jane')
        ->and($user->last_name)->toEqual('Smith')
        ->and($user->email)->toEqual('jane@example.com')
        ->and($user->roles->pluck('name'))->toContain('manager')
        ->and($user->workspaces)->toHaveCount(2);
});
