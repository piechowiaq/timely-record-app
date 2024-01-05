<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface
{
    public function getSearchedUsers(string $searchInput);

    public function sortUsersByField(Builder $query, string $field, string $direction);

    public function getUsersByProjectWithRoles(Project $project);
}
