<?php

use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.registries.reports.create-any', Workspace::factory()->create()))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->withWorkspaces()->create();
        $user->assignRole($role);
        session(['project_id' => $user->project_id]);

        $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);

        actingAs($user)->
        get(route('workspaces.registries.reports.create-any', $workspace->id))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();

    actingAs($user)->
    get(route('workspaces.registries.reports.create-any', $workspace->id))
        ->assertComponent('Workspaces/Registries/Reports/CreateAny');

});

it('passes workspace to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();

    actingAs($user)->
    get(route('workspaces.registries.reports.create-any', $workspace->id))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace));

});

it('passes registries to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registries = Registry::factory()->count(3)->create();
    $workspace->registries()->attach($registries);

    actingAs($user)->
    get(route('workspaces.registries.reports.create-any', $workspace->id))
        ->assertHasResource('registries', RegistryResource::collection($registries));

});
