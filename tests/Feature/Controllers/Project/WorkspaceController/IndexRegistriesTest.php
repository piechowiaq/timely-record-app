<?php

use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.index-registries', Workspace::factory()->create()))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->withWorkspaces()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('workspaces.index-registries', $user->workspaces->first()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.index-registries', $user->workspaces->first()->id))
        ->assertComponent('Workspaces/IndexRegistries');

});

it('passes registries to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Registry::factory(2)->create(['project_id' => $user->project_id]);
    Registry::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Registry::factory(2)->create();

    $registries = Registry::where('project_id', $user->project_id)->orWhereNull('project_id')->with('reports')->with('workspaces')->get();

    actingAs($user)->
    get(route('workspaces.index-registries', $user->workspaces->first()->id))
        ->assertHasPaginatedResource('registries', RegistryResource::collection($registries));

});

it('passes assigned registriesIds to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();

    $customRegistries = Registry::factory(2)->create(['project_id' => $user->project_id]);
    $registries = Registry::factory(2)->create();

    Registry::factory(2)->create(['project_id' => Project::factory()->create()->id]);

    $workspace->registries()->attach($customRegistries);
    $workspace->registries()->attach($registries);

    actingAs($user)->
    get(route('workspaces.index-registries', $user->workspaces->first()->id))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace->loadMissing('registries')));

});
