<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows a user to view their own project', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('projects.show', $user->project))
        ->assertOk();
});

it('does not allow a user to view another user\'s project', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($user)
        ->get(route('projects.show', $otherUser->project))
        ->assertForbidden();
});

it('allows a user to view their project dashboard', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('projects.dashboard', $user->project))
        ->assertOk();
});

it('does not allow a user to view another dashboard', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    actingAs($user)
        ->get(route('projects.dashboard', $otherUser->project))
        ->assertForbidden();
});
