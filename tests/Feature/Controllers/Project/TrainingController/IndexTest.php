<?php

use App\Http\Resources\TrainingResource;
use App\Models\Project;
use App\Models\Training;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {

    get(route('trainings.index'))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('trainings.index'))
            ->assertForbidden();
    }

});

it('returns a correct component', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    actingAs($user)->
    get(route('trainings.index'))
        ->assertComponent('Projects/Trainings/Index');

});

it('passes projects registries as well as registries with project_id null', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    Training::factory(2)->create(['project_id' => $user->project_id]);
    Training::factory(2)->create(['project_id' => Project::factory()->create()->id]);
    Training::factory(2)->create();

    $registries = Training::where('project_id', $user->project_id)->orWhereNull('project_id')->get();

    actingAs($user)->
    get(route('trainings.index'))
        ->assertHasPaginatedResource('trainings', TrainingResource::collection($registries));

});
