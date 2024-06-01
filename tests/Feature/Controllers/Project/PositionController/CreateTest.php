<?php

use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\DepartmentsAndPositionsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('positions.create'))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('positions.create'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('positions.create'))
        ->assertComponent('Projects/Positions/Create');

});

it('passes auth user projects departments to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);
    $this->seed(DepartmentsAndPositionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);

    $departments = Department::where('project_id', $user->project_id)
        ->orWhereNull('project_id')
        ->get();

    actingAs($user)->
    get(route('positions.create'))
        ->assertHasResource('departments', DepartmentResource::collection($departments));

});
