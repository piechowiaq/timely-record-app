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
        // Check if the user can view the project
        $canViewProject = $user->can('view project');

        // Additional conditions: user must have the 'user' role and exactly one workspace
        $isUserRoleWithSingleWorkspace = $user->hasRole('user') && $user->workspaces()->count() > 1;

        // Return true if the user can view the project AND is a 'user' with exactly one workspace
        return $canViewProject && $isUserRoleWithSingleWorkspace;
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
