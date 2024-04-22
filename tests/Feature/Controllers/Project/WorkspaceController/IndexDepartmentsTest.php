<?php

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Department;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Training;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('workspaces.index-departments', Workspace::factory()->create()))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->withWorkspaces()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('workspaces.index-departments', $user->workspaces->first()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('workspaces.index-departments', $user->workspaces->first()->id))
        ->assertComponent('Projects/Workspaces/IndexDepartments');

});

it('passes departments to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Registry::factory(2)->create(['project_id' => $user->project_id]);
    Registry::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Registry::factory(2)->create();

    $departments = Department::where('project_id', $user->project_id)->orWhereNull('project_id')->with('workspaces')->get();

    actingAs($user)->
    get(route('workspaces.index-departments', $user->workspaces->first()->id))
        ->assertHasPaginatedResource('departments', DepartmentResource::collection($departments));

});

it('passes assigned departmentsIds to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->withWorkspaces()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $workspace = $user->workspaces->first();

    $customDepartments = Department::factory(2)->create(['project_id' => $user->project_id]);
    $departments = Department::factory(2)->create();

    Training::factory(2)->create(['project_id' => Project::factory()->create()->id]);

    $workspace->departments()->attach($customDepartments);
    $workspace->departments()->attach($departments);

    actingAs($user)->
    get(route('workspaces.index-departments', $user->workspaces->first()->id))
        ->assertHasResource('workspace', WorkspaceResource::make($workspace->loadMissing('departments')));

});
