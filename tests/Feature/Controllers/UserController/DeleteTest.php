<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {

    delete(route('users.destroy', User::factory()->create(
    )))
        ->assertRedirect(route('login'));

});

it('requires authorization', function () {

    $this->seed(DatabaseSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::role($role)->first();

        $user2 = User::factory()->create(['project_id' => $user->project_id]);

        actingAs($user)
            ->delete(route('users.destroy', $user2->id))
            ->assertForbidden();
    }
});

it('deletes a user', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();

    $user2 = User::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('users.destroy', $user2), [
        'password' => PASSWORD,
    ]);

    $this->assertModelMissing($user2);

});

it('soft deletes a user with project-admin or super-admin role', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();
    session(['project_id' => $user->project_id]);

    $roles = ['project-admin', 'super-admin'];

    foreach ($roles as $role) {
        $user2 = User::factory()->create(['project_id' => $user->project_id]);
        $user2->assignRole($role);

        actingAs($user)->delete(route('users.destroy', $user2), [
            'password' => PASSWORD,
        ]);

        $this->assertTrue($user2->fresh()->trashed());
    }

});

it('redirects to the user index page', function () {

    $this->seed(DatabaseSeeder::class);

    $user = User::role('admin')->first();

    $user2 = User::factory()->create(['project_id' => $user->project_id]);

    actingAs($user)->delete(route('users.destroy', $user2), [
        'password' => PASSWORD,
    ])->assertRedirect(route('users.index'));

});
