<?php

use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Training;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.index-trainings', Workspace::factory()->create()))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->withWorkspaces()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('workspaces.index-trainings', $user->workspaces->first()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.index-trainings', $user->workspaces->first()->id))
        ->assertComponent('Projects/Workspaces/IndexTrainings');

});

it('passes trainings to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Registry::factory(2)->create(['project_id' => $user->project_id]);
    Registry::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Registry::factory(2)->create();

    $trainings = Training::where('project_id', $user->project_id)->orWhereNull('project_id')->with('workspaces')->get();

    actingAs($user)->
    get(route('workspaces.index-trainings', $user->workspaces->first()->id))
        ->assertHasPaginatedResource('trainings', RegistryResource::collection($trainings));

});

it('passes assigned trainingsIds to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();

    $customTrainings = Training::factory(2)->create(['project_id' => $user->project_id]);
    $registries = Training::factory(2)->create();

    Training::factory(2)->create(['project_id' => Project::factory()->create()->id]);

    $workspace->trainings()->attach($customTrainings);
    $workspace->trainings()->attach($registries);

    actingAs($user)->
    get(route('workspaces.index-trainings', $user->workspaces->first()->id))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace->loadMissing('trainings')));

});
