<?php

use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('users.edit', User::factory()->create()))
        ->assertRedirect(route('login'));
});

it('returns a correct component', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->assignRole('user');
    $user2->workspaces()->attach($user->workspaces->first());

    actingAs($user)->
    get(route('users.edit', $user2->id))
        ->assertComponent('Users/Edit');

});

it('passes correct user to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->assignRole('user');
    $user2->workspaces()->attach($user->workspaces->first());

    actingAs($user)->
    get(route('users.edit', $user2->id))
        ->assertHasResource('user', UserResource::make($user2));
});

it('passes auth user workspaces to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->assignRole('user');
    $user2->workspaces()->attach($user->workspaces->first());

    actingAs($user)->
    get(route('users.edit', $user2))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($user->workspaces));
});

it('passes eligible roles to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->assignRole('user');
    $user2->workspaces()->attach($user->workspaces->first());

    $roles = Role::whereNotIn('name', ['project-admin', 'super-admin', 'admin'])->get();

    actingAs($user)->
    get(route('users.edit', $user2))
        ->assertHasResource('roles', RoleResource::collection($roles));

});

it('passes auth user workspaces ids to the view', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $user2 = User::factory()->create(['project_id' => $user->project_id]);
    $user2->assignRole('user');
    $user2->workspaces()->attach($user->workspaces->first());

    actingAs($user)->
    get(route('users.edit', $user2))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('workspacesIds', $user->workspaces->pluck('id')->count())
        );
});
