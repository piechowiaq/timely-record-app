<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use App\Services\RegistryService;

class WorkspaceController extends Controller
{
    protected RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    /**
     * Show the dashboard for the specified resource.
     */
    public function dashboard(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $registries = $this->registryService->getRegistriesWithLatestReport($workspace->id);
        $expiringRegistries = $this->registryService->getExpiringRegistries($registries);
        $upToDateRegistries = $this->registryService->getUpToDateRegistries($registries);
        $nonCompliantRegistries = $this->registryService->getNonCompliantRegistries($registries);
        $registryMetrics = $this->registryService->getRegistryMetrics($registries);

        return inertia('Workspaces/Dashboard', [
            'workspaceId' => $workspace->id,
            'nonCompliantRegistries' => RegistryResource::collection($nonCompliantRegistries->loadMissing('reports')->take(3)),
            'expiringRegistries' => RegistryResource::collection($expiringRegistries->loadMissing('reports')->take(3)),
            'upToDateRegistriesCount' => $upToDateRegistries->count(),
            'registriesCount' => $registries->count(),
            'registryMetrics' => $registryMetrics,
            'workspace' => WorkspaceResource::make($workspace),
        ]);
    }
}
