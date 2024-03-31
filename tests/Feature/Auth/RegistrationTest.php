<?php

use App\Models\Project;
use App\Models\User;
use App\Notifications\SendUserRegistrationNotification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $response = $this->post('/register', [
        'project_name' => 'Test Project',
        'first_name' => 'Test User',
        'last_name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $project = User::latest()->first()->project_id;

    $response->assertRedirect(route('projects.dashboard', compact('project')));

});

it('triggers a UserRegistrationNotification when a user is passed', function () {

    $this->seed(RolesAndPermissionsSeeder::class);
    // Arrange
    Notification::fake();

    $project = Project::factory()->create();

    // Assuming you have a User model, and it uses the HasFactory trait
    $authUser = User::factory()->create(['project_id' => $project->id]);
    $authUser->assignRole('project-admin');

    actingAs($authUser);

    // Ensure the newly created user is associated with the same project
    $user = User::factory()->create(['project_id' => $project->id]);

    // Trigger the action that would send the notification
    post(route('registration.send', ['user' => $user->id]));

    // Assert
    Notification::assertSentTo(
        [$user], SendUserRegistrationNotification::class
    );
});

it('redirects correctly after sending the user registration notification', function () {

    $this->seed(RolesAndPermissionsSeeder::class);

    $project = Project::factory()->create();

    // Assuming you have a User model, and it uses the HasFactory trait
    $authUser = User::factory()->create(['project_id' => $project->id]);
    $authUser->assignRole('project-admin');

    actingAs($authUser);

    // Ensure the newly created user is associated with the same project

    $this->assertAuthenticated();

    $user = User::factory()->create(['project_id' => $project->id]);

    $response = post(route('registration.send', $user));

    $response->assertRedirect(route('users.edit', ['project' => $user->project, 'user' => $user]));
    $response->assertSessionHas('success', 'User registration email sent.');
});

it('generates the correct registration URL in the notification', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    Notification::fake();

    post('/register', [
        'project_name' => 'Test Project',
        'first_name' => 'Test User',
        'last_name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    $mockToken = 'mock-token';

    // Mock the password broker
    Password::shouldReceive('createToken')
        ->with(Mockery::on(fn ($user) => $user instanceof User))
        ->andReturn($mockToken);

    // Rest of your test setup
    $user = User::factory()->create();
    $user->notify(new SendUserRegistrationNotification());

    Notification::assertSentTo($user, SendUserRegistrationNotification::class, function ($notification, $channels, $notifiable) use ($mockToken) {
        $mailData = $notification->toMail($notifiable)->toArray();
        $expectedUrl = route('user.registration', ['token' => $mockToken, 'email' => $notifiable->getEmailForVerification()]);

        return $mailData['actionUrl'] === $expectedUrl;
    });
});
