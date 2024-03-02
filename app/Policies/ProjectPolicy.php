<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Ensure the user has the permission to view projects in general.
        $canViewProject = $user->can('view project');

        // Ensure the user has the 'user' role and exactly one workspace.
        $isUserRoleWithSingleWorkspace = $user->hasRole('user') && $user->workspaces()->count() === 1;

        // Ensure the project the user is trying to view is indeed their project.
        $isUsersProject = $user->project_id === $project->id;

        // The user can view the project if all conditions are met: they have the permission,
        // they are a 'user' with exactly one workspace, and the project is theirs.
        return $canViewProject && $isUserRoleWithSingleWorkspace && $isUsersProject;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        //
    }

    public function createWorkspace(User $user, Project $project): bool
    {
        return $user->hasRole('project-admin') || $user->hasRole('admin');
    }
}
