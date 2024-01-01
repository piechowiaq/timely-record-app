<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;

class ProjectService
{
    protected WorkspaceRepositoryInterface $workspaceRepository;

    // Inject the WorkspaceRepositoryInterface instead of the concrete class
    public function __construct(WorkspaceRepositoryInterface $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    public function getWorkspaces(Project $project)
    {
        return $this->workspaceRepository->getWorkspacesByProject($project);
    }
}
