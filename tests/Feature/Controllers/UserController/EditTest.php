<?php

use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;

it('requires authentication', function () {

    $user = User::factory()->create();

    patch(route('users.update', [$user->project_id, $user->id]))
        ->assertRedirect(route('login'));
});

it('can show a user', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)
        ->get(route('users.edit', [$user->project_id, $user]))
        ->assertComponent('Users/Edit');
});

it('passes correct user to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)
        ->get(route('users.edit', ['project' => 1, 'user' => 1]))
        ->assertHasResource('user', UserResource::make(
            User::find(1)
        ));
});

it('passed workspaces to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    actingAs($user)
        ->get(route('users.edit', [$user->project_id, $user]))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($user->project->workspaces));
});
