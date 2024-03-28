<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {

    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => PASSWORD,
    ]);

    $this->assertAuthenticated();

});

it('redirects authenticated user with role of project-admin with no workspace to workspaces.create route', function () {

    $user = User::factory()->create();

    $this->seed(RolesAndPermissionsSeeder::class);

    $user->assignRole('project-admin');

    $response = post('/login', [
        'email' => $user->email,
        'password' => PASSWORD,
    ]);

    // Attempt to access the projects.dashboard route
    $response = get(route('projects.dashboard', ['project' => $user->project]));

    // Assert the user is redirected to the workspaces.create route
    $response->assertRedirect(route('workspaces.create', ['project' => $user->project]));

});

it('redirects authenticated user with workspace to projects.dashboard route', function () {

    $user = User::factory()->withWorkspaces()->create();

    post('/login', [
        'email' => $user->email,
        'password' => PASSWORD,
    ])->assertRedirect(route('projects.dashboard', ['project' => $user->project_id]));

});

it('assigns Admin role to newly registered user ', function () {

    // Run the roles and permissions seeder
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();

    $user->assignRole('admin');

    expect($user->hasRole('admin'))->toBeTrue();

});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {

    $this->seed(RolesAndPermissionsSeeder::class);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
