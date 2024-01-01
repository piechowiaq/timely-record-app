<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\Workspace;

interface WorkspaceRepositoryInterface
{
    public function getWorkspacesByProject(Project $project);

    public function getUpToDateRegistries(Workspace $workspace);

    public function getExpiredRegistries(Workspace $workspace);
}
