<?php

use App\Models\Registry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Health and Safety Policy',
        'description' => 'Doe',
        'validity_period' => 6,
    ];
});

it('requires authentication', function () {

    put(route('registries.update', Registry::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->put(route('registries.update', Registry::factory()->create()))
            ->assertForbidden();
    }

});

it('updates a registry', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    $registryData = value($this->validData);

    actingAs($user)->put(route('registries.update', $registry->id), $registryData);

    $this->assertDatabaseHas(Registry::class, [
        ...$registryData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the registry edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    $registryData = value($this->validData);

    actingAs($user)->put(route('registries.update', $registry->id), $registryData)
        ->assertRedirect(route('registries.edit', $registry->id));
});
