<?php

use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function () {

    $workspace = Workspace::factory()->create();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    put(route('workspaces.registries.reports.update', [$workspace->id, $registry->id, $report->id]))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user'];

    foreach ($roles as $role) {
        $user = User::factory()->withWorkspaces()->create();
        $user->assignRole($role);

        session(['project_id' => $user->project_id]);

        $workspace = $user->workspaces->first();
        $registry = Registry::factory()->create();
        $workspace->registries()->attach($registry);
        $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

        actingAs($user)
            ->put(route('workspaces.registries.reports.update', [$workspace->id, $registry->id, $report->id]))
            ->assertForbidden();
    }
});

it('updates a registry', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);
    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['project_id' => $user->project_id, 'workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    $reportDate = Carbon::yesterday();
    $expiryDate = (clone $reportDate)->addMonths($registry->validity_period);

    $reportData = [
        'report_date' => $reportDate,
        'expiry_date' => $expiryDate,

    ];

    actingAs($user)
        ->put(route('workspaces.registries.reports.update', [$workspace->id, $registry->id, $report->id]), $reportData);

    $this->assertDatabaseHas(Report::class, [
        'report_date' => $reportDate,
        'expiry_date' => $expiryDate,
        'updated_by_user_id' => $user->id,
        'project_id' => $user->project_id,
        'workspace_id' => $workspace->id,
        'registry_id' => $registry->id,
    ]);
});

it('redirects to the reports edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);
    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['project_id' => $user->project_id, 'workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    $reportDate = Carbon::yesterday();
    $expiryDate = (clone $reportDate)->addMonths($registry->validity_period);

    $reportDateFormatted = $reportDate->format('Y-m-d');
    $expiryDateFormatted = $expiryDate->format('Y-m-d');

    $reportData = [
        'report_date' => $reportDateFormatted,
        'expiry_date' => $expiryDateFormatted,

    ];

    actingAs($user)
        ->put(route('workspaces.registries.reports.update', [$workspace->id, $registry->id, $report->id]), $reportData)
        ->assertRedirect(route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id]));
});
