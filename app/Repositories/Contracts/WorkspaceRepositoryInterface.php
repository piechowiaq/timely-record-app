<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\Workspace;

interface WorkspaceRepositoryInterface
{
    public function getWorkspacesByProjectQuery(Project $project);

    public function getWorkspacesIds($workspaces);

    public function getUpToDateRegistries(Workspace $workspace);

    public function getExpiredRegistries(Workspace $workspace);
}
