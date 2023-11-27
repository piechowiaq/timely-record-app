<?php

use App\Models\Registry;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('it shows a list of registers that belong to workspace', function () {

    $user = User::factory()->withWorkspaces(4)->create();
    actingAs($user);

    $workspace = $user->workspaces->first();
    $project = $user->project;

    // Create registries and attach them to the workspace
    $registries = Registry::factory()->count(4)->create();
    $workspace->registries()->attach($registries);

    $response = get(route('registries.index', ['project' => $project->id, 'workspace' => $workspace->id]));

    expect($response->status())->toBe(200);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('Registries/Index')
        ->has('registries', 4)
        ->where('registries.0.name', $registries[0]['name'])
        ->where('registries.0.validity_period', $registries[0]['validity_period'])
        ->where('registries.1.name', $registries[1]['name'])
        ->where('registries.1.validity_period', $registries[1]['validity_period'])
        ->where('registries.2.name', $registries[2]['name'])
        ->where('registries.2.validity_period', $registries[2]['validity_period'])
        ->where('registries.3.name', $registries[3]['name'])
        ->where('registries.3.validity_period', $registries[3]['validity_period']));

});
