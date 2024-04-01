<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Registry;
use App\Models\Report;
use App\Models\User;
use App\Models\Workspace;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function definition(): array
    {
        $recycledRegistries = $this->recycle->get(Registry::class, collect());

        $recycledProjects = $this->recycle->get(Project::class, collect());

        $reportDate = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d');

        $expiryDate = (new \DateTime($reportDate))
            ->modify('+'.$this->faker->randomElement([1, 3, 6, 12, 24]).' months')
            ->format('Y-m-d');

        return [
            'report_date' => $reportDate,
            'expiry_date' => $expiryDate,
            'report_path' => $this->faker->url(),
            'workspace_id' => Workspace::factory(),
            'registry_id' => $recycledRegistries->isNotEmpty()
                ? $recycledRegistries->random()
                : Registry::factory()->create(),
            'project_id' => $recycledProjects->isNotEmpty()
                ? $recycledProjects->random()
                : Project::factory()->create(),
            'created_by_user_id' => User::factory(),
            'updated_by_user_id' => User::factory(),
        ];
    }
}
