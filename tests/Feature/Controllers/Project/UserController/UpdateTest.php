<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->validData = fn () => [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ];
});

it('requires authentication', function () {

    put(route('users.update', User::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(DatabaseSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::role($role)->first();
        $workspaces = $user->workspaces->pluck('id')->toArray();

        $user2 = User::factory()->create(['project_id' => $user->project_id]);
        $user2->workspaces()->sync($workspaces);

        actingAs($user)
            ->put(route('users.update', $user2->id))
            ->assertForbidden();
    }
});

it('updates a user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $workspaces = $user->workspaces->pluck('id')->toArray();

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->workspaces()->sync($workspaces);

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->put(route('users.update', $user2->id), $userData);

    $this->assertDatabaseHas(User::class, [
        ...$data,
        'project_id' => $user->project_id,
    ]);
});

it('updates role of user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $project = $user->project;
    $workspaces = $user->workspaces->pluck('id')->toArray();

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->workspaces()->sync($workspaces);

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->put(route('users.update', $user2->id), $userData);

    expect($user2->hasRole('user'))->toBeTrue();

});

it('syncs workspaces with user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $workspaces = $user->workspaces->pluck('id')->toArray();

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->workspaces()->sync($workspaces);

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->put(route('users.update', $user2->id), $userData);

    $user2WorkspacesIds = $user2->workspaces->pluck('id')->toArray();

    foreach ($workspaces as $workspace) {
        expect($user2WorkspacesIds)->toContain($workspace);
    }
});

it('redirects to the user edit page', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $workspaces = $user->workspaces->pluck('id')->toArray();

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->workspaces()->sync($workspaces);

    $data = value($this->validData);

    $userData = [
        ...$data,
        'role' => 'user',
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->put(route('users.update', $user2->id), $userData)
        ->assertRedirect(route('users.edit', $user2->id));
});
