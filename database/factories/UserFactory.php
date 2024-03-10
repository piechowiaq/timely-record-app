<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'project_id' => Project::factory(),
            'email_verified_at' => now(),
            'password' => Hash::make('a'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the factory to add workspaces to the user after creating it.
     *
     * @param  int  $count  The number of workspaces to create and attach.
     */
    public function withWorkspaces(int $count = 1): Factory
    {
        return $this->afterCreating(function (User $user) use ($count) {
            $workspaces = Workspace::factory()->count($count)->create([
                'project_id' => $user->project_id,
            ]);

            $user->workspaces()->attach($workspaces);
        });
    }

    /**
     * Assign roles to the user after creation.
     */
    public function withRoles(): static
    {
        return $this->afterCreating(function (User $user) {

            $roles = ['manager', 'user', 'admin'];
            $user->assignRole($roles[array_rand($roles)]);
        });
    }
}
