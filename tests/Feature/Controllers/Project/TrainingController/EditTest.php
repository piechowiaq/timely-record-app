<?php

use App\Http\Resources\TrainingResource;
use App\Models\Training;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('trainings.edit', Training::factory()->create()))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('trainings.edit', Training::factory()->create()))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('trainings.edit', $training->id))
        ->assertComponent('Projects/Trainings/Edit');

});

it('passes correct training view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('trainings.edit', $training->id))
        ->assertHasResource('training', TrainingResource::make($training));
});
