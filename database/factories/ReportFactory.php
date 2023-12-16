<?php

namespace Database\Factories;

use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'report_date' => $this->faker->date(),
            'expiry_date' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),

            'notes' => $this->faker->text(),
            'workspace_id' => Workspace::factory(),
            'registry_id' => Registry::factory(),
            'created_by_user_id' => User::factory(),
            'updated_by_user_id' => User::factory(),
        ];
    }
}
