<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getUsersByProjectWithRolesQuery(Project $project): HasMany
    {
        return $project->users()->where('users.id', '<>', auth()->id())
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'project-admin');
            })
            ->with('roles');
    }

    public function getUserWithWorkspacesAndRoles(User $user): User
    {
        return $user->load('workspaces', 'roles');
    }
}
