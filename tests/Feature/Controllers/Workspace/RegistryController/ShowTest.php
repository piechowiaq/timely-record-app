<?php

use App\Http\Resources\RegistryResource;
use App\Http\Resources\ReportResource;
use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.registries.show', [Workspace::factory()->create(), Registry::factory()->create()]))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        session(['project_id' => $user->project_id]);

        $workspace = Workspace::factory()->create(['project_id' => $user->project_id]);
        $registry = Registry::factory()->create();
        $workspace->registries()->attach($registry);

        actingAs($user)
            ->get(route('workspaces.registries.show', [$workspace->id, $registry->id]))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $roles = ['user', 'manager', 'admin'];

    foreach ($roles as $role) {

        $user->assignRole($role);

        $workspace = $user->workspaces->first();
        $registry = Registry::factory()->create();
        $workspace->registries()->attach($registry);

        actingAs($user)
            ->get(route('workspaces.registries.show', [$workspace->id, $registry->id]))
            ->assertComponent('Workspaces/Registries/Show');
    }
});

it('passes correct registry to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    actingAs($user)
        ->get(route('workspaces.registries.show', [$workspace->id, $registry->id]))
        ->assertHasResource('registry', RegistryResource::make($registry));
});

it('passes current report to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create([
        'registry_id' => $registry->id,
        'workspace_id' => $workspace->id,
    ]);

    actingAs($user)
        ->get(route('workspaces.registries.show', [$workspace->id, $registry->id]))
        ->assertHasResource('currentReport', ReportResource::make($report));
});
