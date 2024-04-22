<?php

use App\Models\Training;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function () {

    put(route('workspaces.sync-trainings', Workspace::factory()->create(
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
            ->put(route('workspaces.sync-trainings', Workspace::factory()->create(['project_id' => $user->project_id])->id))
            ->assertForbidden();
    }

});

it('updates a workspace trainings', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('project-admin');

    $workspace = $user->workspaces->first();

    $trainingsIds = Training::factory(2)->create()->pluck('id')->toArray();

    actingAs($user)->put(route('workspaces.sync-trainings', $workspace->id), $trainingsIds);

    $workspace->trainings()->sync($trainingsIds);

    $workspaceTrainingsIds = $workspace->trainings->pluck('id')->toArray();

    foreach ($trainingsIds as $trainingId) {
        expect($workspaceTrainingsIds)->toContain($trainingId);
    }

});

it('redirects to the workspace edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('project-admin');

    $workspace = $user->workspaces->first();

    $trainingsIds = Training::factory(2)->create()->pluck('id')->toArray();

    actingAs($user)->put(route('workspaces.sync-trainings', $workspace->id), $trainingsIds)
        ->assertRedirect(route('workspaces.index-trainings', $workspace->id));
});
