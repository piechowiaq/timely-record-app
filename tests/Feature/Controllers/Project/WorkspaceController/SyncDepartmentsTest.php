<?php

use App\Models\Department;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function () {

    put(route('workspaces.sync-departments', Workspace::factory()->create(
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
            ->put(route('workspaces.sync-departments', Workspace::factory()->create(['project_id' => $user->project_id])->id))
            ->assertForbidden();
    }

});

it('updates a workspace departments', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('project-admin');

    $workspace = $user->workspaces->first();

    $departmentsIds = Department::factory(2)->create()->pluck('id')->toArray();

    actingAs($user)->put(route('workspaces.sync-registries', $workspace->id), $departmentsIds);

    $workspace->departments()->sync($departmentsIds);

    $workspaceDepartmentsIds = $workspace->departments->pluck('id')->toArray();

    foreach ($departmentsIds as $departmentId) {
        expect($workspaceDepartmentsIds)->toContain($departmentId);
    }

});

it('redirects to the workspace edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('project-admin');

    $workspace = $user->workspaces->first();

    $departmentsIds = Department::factory(2)->create()->pluck('id')->toArray();

    actingAs($user)->put(route('workspaces.sync-departments', $workspace->id), $departmentsIds)
        ->assertRedirect(route('workspaces.index-departments', $workspace->id));
});
