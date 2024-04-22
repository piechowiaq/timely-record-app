<?php

use App\Models\Department;
use App\Models\User;
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

it('redirects to the department index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $departmentData = value($this->validData);

    actingAs($user)->post(route('departments.store'), $departmentData)
        ->assertRedirect(route('departments.index'));
});
