<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
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

    public function getUserWorkspaces(Project $project, User $user)
    {
        return $this->workspaceRepository->getUserWorkspacesByProjectQuery($project, $user)->get();
    }

    public function getWorkspacesWithUpToDateMetrics(Project $project)
    {
        return $this->getWorkspaces($project)->map(function ($workspace) {

            $workspace->upToDateRegistriesMetrics = $this->workspaceService->calculateUpToDateRegistriesMetrics($workspace);

            return $workspace;
        });
    }

    public function getUserWorkspacesWithUpToDateMetrics(Project $project, User $user)
    {
        return $this->getUserWorkspaces($project, $user)->map(function ($workspace) {

            $workspace->upToDateRegistriesMetrics = $this->workspaceService->calculateUpToDateRegistriesMetrics($workspace);

            return $workspace;
        });
    }
}
