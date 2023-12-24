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
     *
     * @throws \Exception
     */
    public function definition(): array
    {
        $registry = Registry::query()->inRandomOrder()->first() ?? Registry::factory()->create();

        // Get a random Workspace model from the database, or create a new one if none exists
        $workspace = Workspace::query()->inRandomOrder()->first() ?? Workspace::factory()->create();

        // Define report date
        // Define report date within the last 2 years
        $reportDate = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');

        // Calculate expiry date based on report date and registry's validity period
        $expiryDate = (new \DateTime($reportDate))
            ->modify('+'.$registry->validity_period.' months')
            ->format('Y-m-d');

        return [
            'report_date' => $reportDate,
            'expiry_date' => $expiryDate,
            'notes' => $this->faker->text(),
            'workspace_id' => $workspace->id,
            'registry_id' => $registry->id,
            'created_by_user_id' => User::factory(),
            'updated_by_user_id' => User::factory(),
        ];
    }
}
