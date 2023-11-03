<?php

use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

it('user with no workspace can store a workspace', function () {

    $user = User::factory()->create();
    actingAs($user);

    usleep(1000000);

    $response = post(route('workspaces.store', ['project' => $user->project_id]), [
        'name' => fake()->company,
        'location' => fake()->city,
    ]);

    $workspace = Workspace::latest()->first();

    $response->assertRedirect(route('workspaces.dashboard', [
        'project' => $user->project_id,
        'workspace' => $workspace->id,
    ]))->assertSessionHas('success', 'Workspace created.');

    expect(Workspace::latest()->first())
        ->name->toBeString()->not->toBeEmpty()
        ->location->toBeString()->not->toBeEmpty()
        ->project_id->toBeInt()->tobe($user->project_id);
});

it('user with workspaces can store a workspace', function () {

    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    usleep(1000000);

    $response = post(route('workspaces.store', ['project' => $user->project_id]), [
        'name' => fake()->company,
        'location' => fake()->city,
    ]);

    $response->assertRedirect(route('projects.show', [
        'project' => $user->project_id,
    ]))->assertSessionHas('success', 'Workspace created.');

    expect(Workspace::latest()->first())
        ->name->toBeString()->not->toBeEmpty()
        ->location->toBeString()->not->toBeEmpty()
        ->project_id->toBeInt()->tobe($user->project_id);
});

todo('can update a workspace');
todo('can delete a workspace');
