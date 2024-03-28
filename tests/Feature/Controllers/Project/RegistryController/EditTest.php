<?php

use App\Http\Resources\RegistryResource;
use App\Models\Registry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('registries.edit', Registry::factory()->create()))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('registries.edit', Registry::factory()->create()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('registries.edit', $registry->id))
        ->assertComponent('Registries/Edit');

});

it('passes correct registry to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('registries.edit', $registry->id))
        ->assertHasResource('registry', RegistryResource::make($registry));
});
