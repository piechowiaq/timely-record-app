<?php

namespace App\Repositories\Contracts;

use App\Models\Project;

interface WorkspaceRepositoryInterface
{
    public function getWorkspacesByProject(Project $project);
}
