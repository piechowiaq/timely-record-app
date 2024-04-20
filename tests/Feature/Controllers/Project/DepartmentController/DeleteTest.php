<?php

use App\Models\Training;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    delete(route('trainings.destroy', Training::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        $training = Training::factory()->create(['project_id' => $user->project_id]);

        actingAs($user)
            ->delete(route('trainings.destroy', $training->id))
            ->assertForbidden();
    }
});

it('deletes a training', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('trainings.destroy', $training->id), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($training);

});

it('redirects to the training index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $training = Training::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('trainings.destroy', $training->id), [
        'password' => PASSWORD,
    ])->assertRedirect(route('trainings.index'));

});
