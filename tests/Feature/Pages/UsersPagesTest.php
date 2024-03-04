<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});
it('admin has users index page', function () {

    $user = User::factory()->withWorkspaces()->create();
    actingAs($user);
    $user->assignRole('admin');

    $response = get(route('users.index', ['project' => $user->project_id]));

    expect($response->status())->toBe(200);
});
