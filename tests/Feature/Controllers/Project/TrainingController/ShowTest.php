<?php

use App\Http\Resources\TrainingResource;
use App\Models\Registry;
use App\Models\Training;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('trainings.show', Registry::factory()->create()))
        ->assertRedirect(route('login'));
});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $roles = ['user', 'manager', 'admin'];

    foreach ($roles as $role) {

        $user->assignRole($role);

        actingAs($user)
            ->get(route('trainings.show', Training::factory()->create()))
            ->assertComponent('Projects/Trainings/Show');
    }
});

it('passes correct training to the view', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('trainings.show', $training->id))
        ->assertHasResource('training', TrainingResource::make($training));
});
