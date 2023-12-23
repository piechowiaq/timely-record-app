<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            RegistrySeeder::class,
        ]);

        $project = Project::factory()->create();
        $workspaces = Workspace::factory(6)->recycle($project)->create();

        $user = User::factory()->recycle($project)->create([
            'first_name' => 'Bartosz',
            'last_name' => 'Piechowiak',
            'email' => 'test@timelyrecord.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->workspaces()->attach($workspaces);

        $registries = Registry::all(); // Get all registries

        // Loop through each workspace
        foreach ($workspaces as $workspace) {
            // Decide the number of registries to attach (randomly)
            $numberOfRegistriesToAttach = rand(1, $registries->count());

            // Get random registries
            $registriesToAttach = $registries->random($numberOfRegistriesToAttach)->pluck('id');

            // Attach the registries to the workspace
            $workspace->registries()->attach($registriesToAttach);
        }

    }
}
