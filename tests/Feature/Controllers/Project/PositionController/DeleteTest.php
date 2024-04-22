<?php

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    delete(route('departments.destroy', Department::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        $department = Department::factory()->create(['project_id' => $user->project_id]);

        actingAs($user)
            ->delete(route('departments.destroy', $department->id))
            ->assertForbidden();
    }
});

it('deletes a department', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('departments.destroy', $department->id), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($department);

});

it('redirects to the department index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $department = Department::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('departments.destroy', $department->id), [
        'password' => PASSWORD,
    ])->assertRedirect(route('departments.index'));

});
