<?php

use App\Http\Resources\RegistryResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('registries.index'))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('registries.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('registries.index'))
        ->assertComponent('Registries/Index');

});

it('passes projects registries as well as registries with project_id null', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Registry::factory(2)->create(['project_id' => $user->project_id]);
    Registry::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Registry::factory(2)->create();

    $registries = Registry::where('project_id', $user->project_id)->orWhereNull('project_id')->get();

    actingAs($user)->
    get(route('registries.index'))
        ->assertHasPaginatedResource('registries', RegistryResource::collection($registries));

});
