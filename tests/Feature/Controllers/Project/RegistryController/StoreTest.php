<?php

use App\Models\Registry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Health and Safety Policy',
        'description' => 'Doe',
        'validity_period' => 6,
    ];
});

it('requires authentication', function () {

    post(route('registries.store', Registry::factory()->create(
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
            ->post(route('registries.store', Registry::factory()->create(
            )))
            ->assertForbidden();
    }
});

it('stores a registry', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $registryData = value($this->validData);

    actingAs($user)->post(route('registries.store'), $registryData);

    $this->assertDatabaseHas(Registry::class, [
        ...$registryData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the registry index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $registryData = value($this->validData);

    actingAs($user)->post(route('registries.store'), $registryData)
        ->assertRedirect(route('registries.index'));
});
