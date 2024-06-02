<?php

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('departments.show', Department::factory()->create()))
        ->assertRedirect(route('login'));
});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $roles = ['user', 'manager', 'admin'];

    foreach ($roles as $role) {

        $user->assignRole($role);

        actingAs($user)
            ->get(route('departments.show', Department::factory()->create()))
            ->assertComponent('Projects/Departments/Show');
    }
});

it('passes correct department to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('departments.show', $department->id))
        ->assertHasResource('department', DepartmentResource::make($department));
});
