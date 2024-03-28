<?php

use App\Http\Resources\RegistryResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    $workspace = Workspace::factory()->create();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    get(route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id]))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->withWorkspaces()->create();
        $user->assignRole($role);

        $workspace2 = Workspace::factory()->create(['project_id' => $user->project_id]);
        $registry2 = Registry::factory()->create();
        $workspace2->registries()->attach($registry2);
        $report2 = Report::factory()->create(['workspace_id' => $workspace2->id, 'registry_id' => $registry2->id]);

        actingAs($user)->
            get(route('workspaces.registries.reports.edit', [$workspace2->id, $registry2->id, $report2->id]))
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
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    actingAs($user)->
    get(route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id]))
        ->assertComponent('Workspaces/Registries/Reports/Edit');

});

it('passes correct workspace to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    actingAs($user)->
    get(route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id]))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace));
});

it('passes correct registry to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    actingAs($user)->
    get(route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id]))
        ->assertHasResource('registry', RegistryResource::make($registry));
});

it('passes correct report to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    actingAs($user)->
    get(route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id]))
        ->assertHasResource('report', ReportResource::make($report));
});
