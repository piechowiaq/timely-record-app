<?php

use App\Models\Registry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    delete(route('registries.destroy', Registry::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        $registry = Registry::factory()->create(['project_id' => $user->project_id]);

        actingAs($user)
            ->delete(route('registries.destroy', $registry->id))
            ->assertForbidden();
    }
});

it('deletes a registry', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('registries.destroy', $registry->id), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($registry);

});

it('redirects to the registry index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('registries.destroy', $registry->id), [
        'password' => PASSWORD,
    ])->assertRedirect(route('registries.index'));

});
