<?php

use App\Models\User;
use App\Models\Workspace;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

it('can store a first workspace', function () {

    $user = User::factory()->create();
    actingAs($user);

    usleep(1000000);

    $response = post(route('workspaces.store', ['project' => $user->project_id]), [
        'name' => fake()->company(),
        'location' => fake()->city(),
    ]);

    $workspace = Workspace::latest()->first();

    $response->assertRedirect(route('projects.dashboard', [
        'project' => $user->project_id,

    ]))->assertSessionHas('success', 'Workspace created.');

    expect(Workspace::latest()->first())
        ->name->toBeString()->not->toBeEmpty()
        ->location->toBeString()->not->toBeEmpty()
        ->project_id->toBeInt()->tobe($user->project_id);
});

it('can store a another workspace', function () {

    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);

    usleep(1000000);

    $data = [
        'name' => fake()->company(),
        'location' => fake()->city(),
    ];

    post(route('workspaces.store', ['project' => $user->project_id]), $data)
        ->assertRedirect(route('workspaces.index', [
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

it('can delete a workspace', function () {

    $user = User::factory()->withWorkspaces()->create();
    $password = 'password';
    $user->password = bcrypt($password);

    actingAs($user);

    $workspace = Workspace::latest()->first();

    delete(route('workspaces.destroy', [
        'project' => $user->project,
        'workspace' => $workspace,
    ]), [
        'password' => $password,
    ])->assertRedirect(route('workspaces.index', $user->project_id))->assertSessionHas('success', 'Workspace deleted.');

    expect(Workspace::find($workspace->id))->toBeNull();
});
