<?php

namespace App\Repositories\Contracts;

use App\Models\Project;

interface UserRepositoryInterface
{
    public function getUsersByProjectWithRolesQuery(Project $project): \Illuminate\Database\Eloquent\Relations\HasMany;
}
