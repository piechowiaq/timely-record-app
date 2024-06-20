<?php

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Executive Chef',
    ];
});

it('requires authentication', function () {

    patch(route('positions.update', Position::factory()->create(
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
            ->put(route('positions.update', Position::factory()->create()))
            ->assertForbidden();
    }

});

it('updates a position', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $position = Position::factory()->create(['project_id' => $user->project_id]);

    $positionData = value($this->validData);

    actingAs($user)->patch(route('positions.update', $position->id), $positionData);

    $this->assertDatabaseHas(Department::class, [
        ...$positionData,
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
