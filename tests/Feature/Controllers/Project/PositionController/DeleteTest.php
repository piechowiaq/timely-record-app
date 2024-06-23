<?php

use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    delete(route('positions.destroy', Position::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        $position = Position::factory()->create(['project_id' => $user->project_id]);

        actingAs($user)
            ->delete(route('positions.destroy', $position->id))
            ->assertForbidden();
    }
});

it('deletes a position', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $position = Position::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('positions.destroy', $position->id), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($position);

});

it('redirects to the position index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $position = Position::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('positions.destroy', $position->id), [
        'password' => PASSWORD,
    ])->assertRedirect(route('positions.index'));

});
