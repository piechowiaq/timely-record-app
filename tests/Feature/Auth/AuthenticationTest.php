<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

});

it('redirects authenticated user with no workspace to workspaces.creat route', function () {

    $project = Project::factory()->create();

    $user = User::factory()->for($project, 'project')->create(); // set the project for the user

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertRedirect(route('workspaces.create', ['project' => $user->project]));

});

it('redirects authenticated user with workspace to projects.dashboard route', function () {

    $project = Project::factory()->create();

    $user = User::factory()->for($project, 'project')->create();

    $workspace = Workspace::factory()->for($project, 'project')->create();

    $user->workspaces()->attach($workspace->id);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    $response->assertRedirect(route('projects.dashboard', ['project' => $project->id]));

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
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
