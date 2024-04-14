<?php

use App\Models\Registry;
use App\Models\Training;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'Fire Awareness',
        'description' => 'Mandatory for all staff',
        'validity_period' => 6,
    ];
});

it('requires authentication', function () {

    post(route('trainings.store', Registry::factory()->create(
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
            ->post(route('trainings.store', Training::factory()->create(
            )))
            ->assertForbidden();
    }
});

it('stores a training', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $trainingData = value($this->validData);

    actingAs($user)->post(route('trainings.store'), $trainingData);

    $this->assertDatabaseHas(Training::class, [
        ...$trainingData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the training index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $trainingData = value($this->validData);

    actingAs($user)->post(route('trainings.store'), $trainingData)
        ->assertRedirect(route('trainings.index'));
});
