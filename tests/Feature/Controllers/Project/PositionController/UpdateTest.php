<?php

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Kitchen',
    ];
});

it('requires authentication', function () {

    patch(route('departments.update', Department::factory()->create(
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
            ->put(route('departments.update', Department::factory()->create()))
            ->assertForbidden();
    }

});

it('updates a department', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    $departmentData = value($this->validData);

    actingAs($user)->patch(route('departments.update', $department->id), $departmentData);

    $this->assertDatabaseHas(Department::class, [
        ...$departmentData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the department edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    $departmentData = value($this->validData);

    actingAs($user)->patch(route('departments.update', $department->id), $departmentData)
        ->assertRedirect(route('departments.edit', $department->id));
});
