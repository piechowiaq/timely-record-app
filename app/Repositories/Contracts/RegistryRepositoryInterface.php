<?php

namespace App\Repositories\Contracts;

use App\Models\Workspace;

interface RegistryRepositoryInterface
{
    public function getWorkspaceRegistriesQuery(Workspace $workspace): \Illuminate\Database\Query\Builder;
}
