<?php

use App\Http\Resources\RegistryResource;
use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.registries.index', Workspace::factory()->create()))
        ->assertRedirect(route('login'));

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');

    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.registries.index', $user->workspaces->first()->id))
        ->assertComponent('Workspaces/Registries/Index');

});

it('passes a registries to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('user');

    session(['project_id' => $user->project_id]);

    Registry::factory(2)->create(['project_id' => $user->project_id]);
    Registry::factory(2)->create();

    $registries = Registry::all();
    $workspace = $user->workspaces->first();

    $workspace->registries()->attach($registries);

    actingAs($user)->
    get(route('workspaces.registries.index', $workspace->id))
        ->assertHasPaginatedResource('registries', RegistryResource::collection($registries->loadMissing('reports')));

});
