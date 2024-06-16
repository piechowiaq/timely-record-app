<?php

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = fn () => [
        'name' => 'General Manager',
        'department_id' => Department::factory()->create()->id,
    ];
});

it('requires authentication', function () {

    post(route('positions.store', Position::factory()->create(
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
            ->post(route('positions.store', Position::factory()->create(
            )))
            ->assertForbidden();
    }
});

it('stores a position', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    session(['project_id' => $user->project_id]);

    $positionData = value($this->validData);

    actingAs($user)->post(route('positions.store'), $positionData);

    $this->assertDatabaseHas(Position::class, [
        ...$positionData,
        'project_id' => $user->project_id,
    ]);
});

it('redirects to the position index page', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');
    session(['project_id' => $user->project_id]);

    $positionData = value($this->validData);

    actingAs($user)->post(route('positions.store'), $positionData)
        ->assertRedirect(route('positions.index'));
});
