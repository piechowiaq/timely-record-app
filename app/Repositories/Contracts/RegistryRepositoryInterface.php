<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

interface RegistryRepositoryInterface
{
    public function getWorkspaceRegistriesQuery(Workspace $workspace): Builder;

    public function getRegistriesByProjectQuery(Project $project): HasMany;
}
