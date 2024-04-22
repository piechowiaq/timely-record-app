<?php

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('positions.index'))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('positions.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('departments.index'))
        ->assertComponent('Projects/Departments/Index');

});

it('passes projects departments as well as departments with project_id null', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Department::factory(2)->create(['project_id' => $user->project_id]);
    Department::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Department::factory(2)->create();

    $departments = Department::where('project_id', $user->project_id)->orWhereNull('project_id')->get();

    actingAs($user)->
    get(route('departments.index'))
        ->assertHasPaginatedResource('departments',
            DepartmentResource::collection($departments));

});
