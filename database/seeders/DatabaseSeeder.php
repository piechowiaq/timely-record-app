<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RegistrySeeder::class,
            RolesAndPermissionsSeeder::class,
        ]);

        $projects = Project::factory(3)->create();
        $users = User::factory(25)->recycle($projects)->create();
        $workspaces = Workspace::factory(15)->recycle($projects)->create();

        $roles = Role::whereNotIn('name', ['project-admin', 'super-admin'])->get();

        foreach ($projects as $project) {

            $projectUsers = $users->where('project_id', $project->id);

            $projectAdmin = $projectUsers->first()->assignRole('project-admin');

            $projectUsers->reject(function ($user) use ($projectAdmin) {
                return $user->id === $projectAdmin->id;
            })->each(function ($user) use ($roles) {

                $user->assignRole($roles->random());
            });
        }

        foreach ($users as $user) {

            $matchingWorkspaces = $workspaces->filter(function ($workspace) use ($user) {
                return $workspace->project_id === $user->project_id;
            });

            if ($matchingWorkspaces->isNotEmpty()) {

                $selectedWorkspace = $matchingWorkspaces->random();
                $user->workspaces()->attach($selectedWorkspace->id);
            }
        }
    }
}
