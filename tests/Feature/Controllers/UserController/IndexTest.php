<?php

use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

it('requires authentication', function () {

    get(route('users.index', Project::factory()->create()))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);
        session(['project_id' => $user->project_id]);

        actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('users.index'))
        ->assertComponent('Users/Index');

});

it('passes project users to the view excluding project-admin role and auth user', function () {

    $user = User::factory()->withWorkspaces(2)->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    User::factory()->count(5)->withRoles()->create([
        'project_id' => $user->project_id,
    ]);

    $users = User::with('roles')->where('project_id', $user->project_id)->whereDoesntHave('roles', function ($query) {
        $query->where('name', '=', 'project-admin');
    })->where('id', '<>', $user->id)->get();

    actingAs($user)->
    get(route('users.index'))
        ->assertHasPaginatedResource('users', UserResource::collection($users));

});
