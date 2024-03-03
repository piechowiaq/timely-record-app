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
        $registry = $recycledRegistries->isNotEmpty()
            ? $recycledRegistries->random()
            : Registry::factory()->create();
        //        $registry = Registry::query()->inRandomOrder()->first() ?? Registry::factory()->create();
        $recycledProjects = $this->recycle->get(Project::class, collect());
        $project = $recycledProjects->isNotEmpty()
            ? $recycledProjects->random()
            : Project::factory()->create();
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
            'filename' => $this->faker->word,
            'url' => $this->faker->url,
            'extension' => $this->faker->fileExtension,
            'workspace_id' => Workspace::factory(),
            'registry_id' => $registry->id,
            'project_id' => $project->id,
            'created_by_user_id' => User::factory(),
            'updated_by_user_id' => User::factory(),
        ];
    }
}
