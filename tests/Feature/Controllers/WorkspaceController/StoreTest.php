<?php

use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Radisson Blu',
        'location' => 'Warszawa',
    ];
});

it('requires authentication', function () {

    post(route('workspaces.store', Workspace::factory()->create(
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
            ->post(route('workspaces.store', Workspace::factory()->create(
            )))
            ->assertForbidden();
    }
});

it('stores a workspace', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $workspaceData = value($this->validData);

    actingAs($user)->post(route('workspaces.store'), $workspaceData);

    $this->assertDatabaseHas(Workspace::class, [
        ...$workspaceData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the workspaces index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $workspaceData = value($this->validData);

    actingAs($user)->post(route('workspaces.store'), $workspaceData)
        ->assertRedirect(route('workspaces.index'));
});
