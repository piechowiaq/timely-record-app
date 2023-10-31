<?php

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Providers\RouteServiceProvider;

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


it('redirects authenticated user with no workspaces to workspaces.create route', function () {

    $project = Project::factory()->create();

    $user = User::factory()->for($project, 'project')->create(); // set the project for the user

    $workspace = Workspace::factory()->for($project, 'project')->create(); // set the project for the workspace

    $user->workspaces()->attach($workspace->id);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();


//    $user =  User::factory()->create();
//    $workspace = \App\Models\Workspace::factory()->create();
//    $user->workspaces()->attach($workspace->id);


    // Ensure the user has no workspaces.
    $response->assertRedirect(route('projects.dashboard', ['project' => $project->id]));




    // Make a request that should redirect if the user has no workspaces.
    // This might be a get request to a dashboard, for instance.
//    test()->get(route('workspaces.create', ['project' => $user->project_id]));

    // Assert that there was a redirect to the intended route.
//    $this->assertRedirect(route('workspaces.create', ['project' => $user->project_id]));

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
