<?php

use App\Http\Resources\PositionResource;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('positions.show', Position::factory()->create()))
        ->assertRedirect(route('login'));
});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $roles = ['user', 'manager', 'admin'];

    foreach ($roles as $role) {

        $user->assignRole($role);

        actingAs($user)
            ->get(route('positions.show', Position::factory()->create()))
            ->assertComponent('Projects/Positions/Show');
    }
});

it('passes correct training to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $position = Position::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('positions.show', $position->id))
        ->assertHasResource('position', PositionResource::make($position));
});
