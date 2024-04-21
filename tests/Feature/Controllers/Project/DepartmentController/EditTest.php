<?php

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('departments.edit', Department::factory()->create()))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('departments.edit', Department::factory()->create()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('departments.edit', $department->id))
        ->assertComponent('Projects/Departments/Edit');

});

it('passes correct department to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('departments.edit', $department->id))
        ->assertHasResource('department', DepartmentResource::make($department));
});
