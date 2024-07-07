<?php

use App\Models\Department;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Kitchen',
    ];
});

it('requires authentication', function () {

    post(route('departments.store', Department::factory()->create(
    )))->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->post(route('departments.store', Department::factory()->create(
            )))
            ->assertForbidden();
    }
});

it('stores a department', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['super-admin', 'project-admin', 'admin'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        session(['project_id' => $user->project_id]);

        $departmentData = value($this->validData);

        actingAs($user)->post(route('departments.store'), $departmentData);

        $this->assertDatabaseHas(Department::class, [
            ...$departmentData,
            'project_id' => $user->project_id,
        ]);
    }

});

it('syncs workspaces with the created department for project-admin', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('project-admin');

    session(['project_id' => $user->project_id]);

    $workspaces = Workspace::factory(5)->create(['project_id' => $user->project_id]);

    $user->workspaces()->attach($workspaces->take(2));
    $assignedWorkspaceIds = $user->workspaces->pluck('id')->toArray();

    $departmentData = [
        ...value($this->validData),
        'workspacesIds' => $assignedWorkspaceIds,
    ];

    actingAs($user)->post(route('departments.store'), $departmentData);

    $newDepartment = Department::where('name', 'Kitchen')->first();
    $newDepartmentWorkspaceIds = $newDepartment->workspaces->pluck('id')->toArray();

    expect($newDepartmentWorkspaceIds)->toEqual($assignedWorkspaceIds);
});

it('syncs workspaces with the created department for admin', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);

    $workspaces = Workspace::factory(5)->create(['project_id' => $user->project_id]);

    $user->workspaces()->attach($workspaces->take(2));
    $assignedWorkspaceIds = $user->workspaces->pluck('id')->toArray();

    $departmentData = [
        ...value($this->validData),
        'workspacesIds' => $assignedWorkspaceIds,
    ];

    actingAs($user)->post(route('departments.store'), $departmentData);
    $newDepartment = Department::where('name', 'Kitchen')->first();
    $newDepartmentWorkspaceIds = $newDepartment->workspaces->pluck('id')->toArray();
    expect($newDepartmentWorkspaceIds)->toEqual($assignedWorkspaceIds);
});

it('redirects to the department index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $departmentData = value($this->validData);

    actingAs($user)->post(route('departments.store'), $departmentData)
        ->assertRedirect(route('departments.index'));
});
