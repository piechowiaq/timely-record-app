<?php

use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use Database\Seeders\RolesAndPermissionsSeeder;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {
    get(route('departments.create'))
        ->assertRedirect(route('login'));
});

it('requires authorization', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['user', 'manager'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('departments.create'))
            ->assertForbidden();
    }
});

it('returns the correct component', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $roles = ['super-admin', 'project-admin', 'admin'];

    foreach ($roles as $role) {
        $user = User::factory()->create();
        $user->assignRole($role);

        actingAs($user)
            ->get(route('departments.create'))
            ->assertComponent('Projects/Departments/Create');
    }
});

it('passes no workspaces to the view for super-admin', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create(['project_id' => null]);
    $user->assignRole('super-admin');

    Workspace::factory(2)->create();

    actingAs($user)
        ->get(route('departments.create'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Projects/Departments/Create')
            ->has('workspaces', 0)
        );
});

it('passes all project workspaces to the view for project-admin', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('project-admin');

    Workspace::factory(2)->create();

    $projectWorkspaces = Workspace::factory(2)->create(['project_id' => $user->project_id]);
    $projectWorkspacesResource = WorkspaceResource::collection($projectWorkspaces);

    actingAs($user)
        ->get(route('departments.create'))
        ->assertHasPaginatedResource('workspaces', $projectWorkspacesResource);
});

it('passes only assigned workspaces to the view for admin', function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->assignRole('admin');

    $workspaces = Workspace::factory(5)->create(['project_id' => $user->project_id]);

    $user->workspaces()->attach($workspaces->take(2));
    $assignedWorkspacesResource = WorkspaceResource::collection($user->workspaces);

    actingAs($user)
        ->get(route('departments.create'))
        ->assertHasPaginatedResource('workspaces', $assignedWorkspacesResource);

});
