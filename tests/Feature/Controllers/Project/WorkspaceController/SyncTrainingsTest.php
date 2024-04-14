<?php

use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function () {

    put(route('workspaces.sync-registries', Workspace::factory()->create(
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
            ->put(route('workspaces.sync-registries', Workspace::factory()->create(['project_id' => $user->project_id])->id))
            ->assertForbidden();
    }

});

it('updates a workspace registries', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('project-admin');

    $workspace = $user->workspaces->first();

    $registriesIds = Registry::factory(2)->create()->pluck('id')->toArray();

    actingAs($user)->put(route('workspaces.sync-registries', $workspace->id), $registriesIds);

    $workspace->registries()->sync($registriesIds);

    $workspaceRegistriesIds = $workspace->registries->pluck('id')->toArray();

    foreach ($registriesIds as $registryId) {
        expect($workspaceRegistriesIds)->toContain($registryId);
    }

});

it('redirects to the workspace edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('project-admin');

    $workspace = $user->workspaces->first();

    $registriesIds = Registry::factory(2)->create()->pluck('id')->toArray();

    actingAs($user)->put(route('workspaces.sync-registries', $workspace->id), $registriesIds)
        ->assertRedirect(route('workspaces.index-registries', $workspace->id));
});
