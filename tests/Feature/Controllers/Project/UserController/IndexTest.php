<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('users.index'))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('users.index'))
        ->assertComponent('Users/Index');

});

it('passes auth user workspaces users to the view excluding project-admin role and auth user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $authUserWorkspaces = $user->workspaces->pluck('id')->toArray();

    $users = User::inWorkspaces($authUserWorkspaces)
        ->with('roles')
        ->with('workspaces')
        ->withRolesEligibleToView('admin')->get();

    actingAs($user)->
    get(route('users.index'))
        ->assertHasPaginatedResource('users', UserResource::collection($users));
});
