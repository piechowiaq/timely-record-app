<?php

namespace App\Services;

use App\Models\Workspace;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;

class WorkspaceService
{
    protected WorkspaceRepositoryInterface $workspaceRepository;

    // Inject the WorkspaceRepositoryInterface instead of the concrete class
    public function __construct(WorkspaceRepositoryInterface $workspaceRepository)
    {
        $this->workspaceRepository = $workspaceRepository;
    }

    public function calculateUpToDateRegistriesMetrics(Workspace $workspace): float
    {
        $countOfUpToDateRegistries = count($this->workspaceRepository->getUpToDateRegistries($workspace));
        $countOfExpiredRegistries = count($this->workspaceRepository->getExpiredRegistries($workspace));

        $totalRegistries = $countOfUpToDateRegistries + $countOfExpiredRegistries;

        if ($totalRegistries == 0) {
            return 0.0; // or any other default value you'd like to return when there's no registry
        }

        return round(($countOfUpToDateRegistries / $totalRegistries) * 100);

    }
}
