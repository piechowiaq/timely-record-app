<?php

use App\Models\Workspace;

use function Pest\Laravel\get;

it('shows workspace name', function () {

    $workspace = Workspace::factory()->create();

    get('workspaces.dashboard', ['project' => $workspace->project_id, 'workspace' => $workspace->id])
        ->assertOk()
        ->assertSeeText([$workspace->name]);
});
