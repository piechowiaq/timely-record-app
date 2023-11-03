<?php

use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

it('user with no workspace can store a workspace', function () {

    $user = User::factory()->create();
    actingAs($user);

    usleep(1000000);

    $response = post(route('workspaces.store', ['project' => $user->project_id]), [
        'name' => fake()->company(),
        'location' => fake()->city(),
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

    $data = [
        'name' => fake()->company(),
        'location' => fake()->city(),
    ];

    post(route('workspaces.store', ['project' => $user->project_id]), $data)
        ->assertRedirect(route('projects.show', [
            'project' => $user->project_id,
        ]))->assertSessionHas('success', 'Workspace created.');

    expect(Workspace::latest()->first())
        ->name->toBeString()->not->toBeEmpty()
        ->location->toBeString()->not->toBeEmpty()
        ->project_id->toBeInt()->tobe($user->project_id);
});

it('can update a workspace', function () {

    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    usleep(1000000);

    $workspace = Workspace::latest()->first();

    $updatedData = [
        'name' => fake()->company(),
        'location' => fake()->city(),
    ];

    patch(route('workspaces.update', ['project' => $user->project_id, 'workspace' => $workspace->id]), $updatedData)
        ->assertRedirect(route('workspaces.edit', [
            'project' => $user->project_id,
            'workspace' => $workspace->id,
        ]))->assertSessionHas('success', 'Workspace updated.');

    $workspace->refresh();

    expect($workspace->name)->toBe($updatedData['name'])
        ->and($workspace->location)->toBe($updatedData['location']);
});

todo('can delete a workspace');
