<?php

use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
});
test('profile page is displayed', function () {
    $user = User::factory()->create();

    $user->assignRole('user');

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Test User',
            'last_name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->first_name);
    $this->assertSame('Test User', $user->last_name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'first_name' => 'Test User',
            'last_name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can soft delete their account', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => PASSWORD,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();

    $this->assertTrue($user->fresh()->trashed());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();
    $user->assignRole('user');
    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
