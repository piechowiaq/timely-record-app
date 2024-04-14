<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'validity_period' => $this->faker->randomElement([1, 3, 6, 12, 24]),

            'project_id' => Project::factory(),
        ];
    }
}
