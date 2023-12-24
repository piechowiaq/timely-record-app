<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hotelSuffixes = ['Hotel', 'Resort', 'Inn', 'Lodge', 'Suites', 'Palace', 'Plaza'];
        $location = $this->faker->city;
        $hotelName = $location.' '.$this->faker->randomElement($hotelSuffixes);

        return [
            'name' => $hotelName,
            'location' => $location,
            'project_id' => Project::factory(),
        ];
    }
}
