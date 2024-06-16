<?php

use App\Http\Resources\PositionResource;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('positions.edit', Position::factory()->create()))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('positions.edit', Position::factory()->create()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $position = Position::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('positions.edit', $position->id))
        ->assertComponent('Projects/Positions/Edit');

});

it('passes correct position to view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $position = Position::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('positions.edit', $position->id))
        ->assertHasResource('position', PositionResource::make($position));
});
