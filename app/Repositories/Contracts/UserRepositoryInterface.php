<?php

namespace App\Repositories\Contracts;

use App\Models\Project;

interface UserRepositoryInterface
{
    public function getUsersByProjectWithRoles(Project $project): \Illuminate\Database\Eloquent\Relations\HasMany;
}
