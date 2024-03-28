<?php

use App\Http\Resources\RegistryResource;
use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.dashboard', Workspace::factory()->create()))
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

        actingAs($user)->
        get(route('workspaces.dashboard', $workspace->id))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');

    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.dashboard', $user->workspaces->first()->id))
        ->assertComponent('Workspaces/Dashboard');

});

it('passes nonCompliantRegistries to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');
    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();

    Report::factory()
        ->create([
            'project_id' => $user->project_id,
            'expiry_date' => now()->subDays(1),
            'workspace_id' => $workspace->id]);
    Registry::factory()->create(['project_id' => $user->project_id]);
    $registries = Registry::all();

    $workspace->registries()->attach($registries);

    actingAs($user)->
    get(route('workspaces.dashboard', $user->workspaces->first()->id))
        ->assertHasResource('nonCompliantRegistries', RegistryResource::collection($registries->loadMissing('reports')->take(3)));

});

it('passes expiringRegistries to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');
    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();

    Report::factory()
        ->create([
            'project_id' => $user->project_id,
            'expiry_date' => now()->addDay(),
            'workspace_id' => $workspace->id]);

    $registries = Registry::all();

    $workspace->registries()->attach($registries);

    actingAs($user)->
    get(route('workspaces.dashboard', $user->workspaces->first()->id))
        ->assertHasResource('expiringRegistries', RegistryResource::collection($registries->loadMissing('reports')->take(3)));

});
