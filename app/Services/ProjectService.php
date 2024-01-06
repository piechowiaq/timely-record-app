<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;

class ProjectService
{
    protected WorkspaceRepositoryInterface $workspaceRepository;

    protected WorkspaceService $workspaceService;

    public function __construct(WorkspaceRepositoryInterface $workspaceRepository, WorkspaceService $workspaceService)
    {
        $this->workspaceRepository = $workspaceRepository;
        $this->workspaceService = $workspaceService;
    }

    public function getWorkspaces(Project $project)
    {
        return $this->workspaceRepository->getWorkspacesByProjectQuery($project)->get();
    }

    public function getWorkspacesWithUpToDateMetrics(Project $project)
    {
        return $this->getWorkspaces($project)->map(function ($workspace) {

            $workspace->upToDateRegistriesMetrics = $this->workspaceService->calculateUpToDateRegistriesMetrics($workspace);

            return $workspace;
        });
    }
}
