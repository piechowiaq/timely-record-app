<?php

use App\Http\Resources\UserResource;

use function Pest\Laravel\get;

it('should rerun the correct component for auth user', function () {
    get(route('users.index', ['project' => 1]))
        ->assertComponent('Users/Index');
});

it('passes correct users to the view', function () {
    $users = User::factory()->count(3)->create();
    get(route('users.index', ['project' => 1]))
        ->assertHasPaginatedResource('users', UserResource::collection($users));
});
