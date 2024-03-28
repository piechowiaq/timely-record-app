<?php

use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    $workspace = Workspace::factory()->create();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    delete(route('workspaces.registries.reports.destroy', [$workspace->id, $registry->id, $report->id]))
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
            ->delete(route('workspaces.registries.reports.destroy', [$workspace->id, $registry->id, $report->id]))
            ->assertForbidden();
    }
});

it('deletes a report', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    actingAs($user)->delete(route('workspaces.registries.reports.destroy', [$workspace->id, $registry->id, $report->id]), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($report);

});

it('redirects to the registry index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);
    $report = Report::factory()->create(['workspace_id' => $workspace->id, 'registry_id' => $registry->id]);

    actingAs($user)->delete(route('workspaces.registries.reports.destroy', [$workspace->id, $registry->id, $report->id]), [
        'password' => PASSWORD,
    ])->assertRedirect(route('workspaces.registries.show', [$workspace->id, $registry->id]));

});
