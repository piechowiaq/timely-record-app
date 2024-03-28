<?php

use App\Http\Resources\RegistryResource;
use App\Models\Registry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('registries.show', Registry::factory()->create()))
        ->assertRedirect(route('login'));
});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $roles = ['user', 'manager', 'admin'];

    foreach ($roles as $role) {

        $user->assignRole($role);

        actingAs($user)
            ->get(route('registries.show', Registry::factory()->create()))
            ->assertComponent('Registries/Show');
    }
});

it('passes correct registry to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $registry = Registry::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('registries.show', $registry->id))
        ->assertHasResource('registry', RegistryResource::make($registry));
});
