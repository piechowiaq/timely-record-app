<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = fn () => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ];
});

it('requires authentication', function () {

    post(route('users.store', User::factory()->create(
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
            ->post(route('users.store', User::factory()->create(
            )))
            ->assertForbidden();
    }
});

it('stores a user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $project = $user->project;
    $workspaces = $user->workspaces->pluck('id')->toArray();

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->post(route('users.store'), $userData);

    $this->assertDatabaseHas(User::class, [
        ...$data,
        'project_id' => $project->id,
    ]);
});

it('assigns role to user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $workspaces = $user->workspaces->pluck('id')->toArray();

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->post(route('users.store'), $userData);

    $newUser = User::where('email', 'john.doe@example.com')->first()->fresh();
    expect($newUser->hasRole('user'))->toBeTrue();
});

it('syncs workspaces with a user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $project = $user->project;
    $workspaces = $user->workspaces->pluck('id')->toArray();

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->post(route('users.store'), $userData);

    $newUser = User::where('email', 'john.doe@example.com')->first()->fresh();

    $newUserWorkspacesIds = $newUser->workspaces->pluck('id')->toArray();

    foreach ($workspaces as $workspace) {
        expect($newUserWorkspacesIds)->toContain($workspace);
    }
});

it('redirects to the user index page', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $project = $user->project;
    $workspaces = $user->workspaces->pluck('id')->toArray();

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->post(route('users.store'), $userData)
        ->assertRedirect(route('users.index'));
});
//it('redirects to user index page', function () {
//
//});
//
//it('can store a user with valid workspaces', function () {
//
//});
//
//it('can store a user with valid role', function () {
//
//});
