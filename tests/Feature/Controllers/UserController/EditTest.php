<?php

use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\User;

use function Pest\Laravel\get;

it('can show a user', function () {

    get(route('users.edit', ['project' => 1, 'user' => 1]))
        ->assertComponent('Users/Edit');
});

it('passes correct user to the view', function () {

    get(route('users.edit', ['project' => 1, 'user' => 1]))
        ->assertHasResource('user', UserResource::make(
            User::find(1)
        ));
});

it('passed workspaces to the view', function () {

    $workspaces = User::find(1)->workspaces;

    get(route('users.edit', ['project' => 1, 'user' => 1]))
        ->assertHasPaginatedResource('workspaces', WorkspaceResource::collection($workspaces));
});
