<?php

use App\Models\Training;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Fire Awareness',
        'description' => 'Mandatory for all staff',
        'validity_period' => 6,
    ];
});

it('requires authentication', function () {

    patch(route('trainings.update', Training::factory()->create(
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
            ->put(route('trainings.update', Training::factory()->create()))
            ->assertForbidden();
    }

});

it('updates a training', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    $trainingData = value($this->validData);

    actingAs($user)->patch(route('trainings.update', $training->id), $trainingData);

    $this->assertDatabaseHas(Training::class, [
        ...$trainingData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the training edit page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    $trainingData = value($this->validData);

    actingAs($user)->patch(route('trainings.update', $training->id), $trainingData)
        ->assertRedirect(route('trainings.edit', $training->id));
});
