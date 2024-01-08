<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\User;

interface UserRepositoryInterface
{
    public function getUsersByProjectWithRolesQuery(Project $project): \Illuminate\Database\Eloquent\Relations\HasMany;

    public function getUserWithWorkspacesAndRoles(User $user);
}
