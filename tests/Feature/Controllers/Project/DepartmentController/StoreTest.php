<?php

use App\Models\Department;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
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
    )))
        ->assertRedirect(route('login'));

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

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $departmentData = value($this->validData);

    actingAs($user)->post(route('departments.store'), $departmentData);

    $this->assertDatabaseHas(Department::class, [
        ...$departmentData,
        'project_id' => $user->project_id,
    ]);
});

it('sync workspaces with department', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $workspaces = $user->workspaces->pluck('id')->toArray();

    $data = value($this->validData);

    $departmentData = [
        ...$data,
        'workspacesIds' => $workspaces,
    ];

    actingAs($user)->post(route('departments.store'), $departmentData);

    $newDepartment = Department::where('name', 'Kitchen')->first()->fresh();

    $newDepartmentWorkspacesIds = $newDepartment->workspaces->pluck('id')->toArray();

    foreach ($workspaces as $workspace) {
        expect($newDepartmentWorkspacesIds)->toContain($workspace);
    }
});

it('stores a department for super-admin', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('super-admin');
    session(['project_id' => null]);

    $departmentData = value($this->validData);

    actingAs($user)->post(route('departments.store'), $departmentData);

    $this->assertDatabaseHas(Department::class, [
        ...$departmentData,
        'project_id' => null,
    ]);
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
