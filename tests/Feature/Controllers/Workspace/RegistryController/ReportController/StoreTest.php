<?php

use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

it('requires authentication', function () {

    $workspace = Workspace::factory()->create();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    post(route('workspaces.registries.reports.store', [$workspace->id, $registry->id]))
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

        $reportDate = Carbon::yesterday();
        $expiryDate = (clone $reportDate)->addMonths($registry->validity_period);

        $reportDateFormatted = $reportDate->format('Y-m-d');
        $expiryDateFormatted = $expiryDate->format('Y-m-d');

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
        $date = Carbon::now()->format('m-d-Y_H-i-s');
        $fileName = preg_replace('/[^A-Za-z0-9\-_.]/', '_', $date.'_'.$user->project_id.'_'.$workspace->id.'_'.$registry->name.'.'.$file->extension());

        $reportData = [
            'report_date' => $reportDateFormatted,
            'expiry_date' => $expiryDateFormatted,
            'report_path' => $file,
            'created_by_user_id' => $user->id,
            'project_id' => $user->project_id,
            'workspace_id' => $workspace->id,
            'registry_id' => $registry->id,
        ];

        actingAs($user)
            ->post(route('workspaces.registries.reports.store', [$workspace->id, $registry->id]), $reportData)
            ->assertForbidden();
    }
});

it('stores a registry', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);
    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    $reportDate = Carbon::yesterday();
    $expiryDate = (clone $reportDate)->addMonths($registry->validity_period);

    $reportDateFormatted = $reportDate->format('Y-m-d');
    $expiryDateFormatted = $expiryDate->format('Y-m-d');

    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
    $date = Carbon::now()->format('m-d-Y_H-i-s');
    $fileName = preg_replace('/[^A-Za-z0-9\-_.]/', '_', $date.'_'.$user->project_id.'_'.$workspace->id.'_'.$registry->name.'.'.$file->extension());

    $reportData = [
        'report_date' => $reportDateFormatted,
        'expiry_date' => $expiryDateFormatted,
        'report_path' => $file,
    ];

    actingAs($user)
        ->post(route('workspaces.registries.reports.store', [$workspace->id, $registry->id]), $reportData);

    $this->assertDatabaseHas(Report::class, [
        'report_date' => $reportDateFormatted,
        'expiry_date' => $expiryDateFormatted,
        'report_path' => $fileName,
        'updated_by_user_id' => $user->id,
        'project_id' => $user->project_id,
        'workspace_id' => $workspace->id,
        'registry_id' => $registry->id,
    ]);
});

it('redirects to the registry index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);
    $workspace = $user->workspaces->first();
    $registry = Registry::factory()->create();
    $workspace->registries()->attach($registry);

    $reportDate = Carbon::yesterday();
    $expiryDate = (clone $reportDate)->addMonths($registry->validity_period);

    $reportDateFormatted = $reportDate->format('Y-m-d');
    $expiryDateFormatted = $expiryDate->format('Y-m-d');

    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');
    $date = Carbon::now()->format('m-d-Y_H-i-s');
    $fileName = preg_replace('/[^A-Za-z0-9\-_.]/', '_', $date.'_'.$user->project_id.'_'.$workspace->id.'_'.$registry->name.'.'.$file->extension());

    $reportData = [
        'report_date' => $reportDateFormatted,
        'expiry_date' => $expiryDateFormatted,
        'report_path' => $file,
        'created_by_user_id' => $user->id,
        'project_id' => $user->project_id,
        'workspace_id' => $workspace->id,
        'registry_id' => $registry->id,
    ];

    actingAs($user)
        ->post(route('workspaces.registries.reports.store', [$workspace->id, $registry->id]), $reportData)
        ->assertRedirect(route('workspaces.registries.show', [$workspace->id, $registry->id]));
});
