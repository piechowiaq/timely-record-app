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

    $workspace = Workspace::factory()->create();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    get(route('workspaces.registries.reports.create', [$workspace->id, $registry->id]))
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
        $registry = Registry::factory()->create();
        $workspace->registries()->attach($registry);

        actingAs($user)->
        get(route('workspaces.registries.reports.create', [$workspace->id, $registry->id]))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    actingAs($user)->
    get(route('workspaces.registries.reports.create', [$workspace->id, $registry->id]))
        ->assertComponent('Workspaces/Registries/Reports/Create');

});

it('passes workspace to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    actingAs($user)->
    get(route('workspaces.registries.reports.create', [$workspace->id, $registry->id]))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace));

});

it('passes registries to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    actingAs($user)->
    get(route('workspaces.registries.reports.create', [$workspace->id, $registry->id]))
        ->assertHasResource('registry', RegistryResource::make($registry));

});
