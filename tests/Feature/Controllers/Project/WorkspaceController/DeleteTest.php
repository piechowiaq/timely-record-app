<?php

use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    delete(route('workspaces.destroy', Workspace::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager', 'admin'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->delete(route('workspaces.destroy', Workspace::factory()->create(['project_id' => $user->project_id])->id))
            ->assertForbidden();
    }
});

it('deletes a workspace', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('project-admin');

    $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
    $user->workspaces()->attach($workspace);

    actingAs($user)->delete(route('workspaces.destroy', $workspace->id), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($workspace);

});

it('redirects to the workspace index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('project-admin');

    $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
    $user->workspaces()->attach($workspace);

    actingAs($user)->delete(route('workspaces.destroy', $workspace->id), [
        'password' => PASSWORD,
    ])->assertRedirect(route('workspaces.index'));

});
