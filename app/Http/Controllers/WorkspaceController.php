<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegistryResource;
use App\Models\Registry;
use App\Models\Workspace;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Workspace::class, 'workspace');
    }

    /**
     * Show the dashboard for the specified resource.
     */
    public function dashboard(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $workspaceId = $workspace->id;

        $registries = Registry::with(['reports' => function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId)
                ->latest('expiry_date');
        }])->whereHas('workspaces', function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        })->get();

        $expiringRegistries = $registries->filter(function ($registry) {
            return ! is_null($registry->reports->first()) && $registry->reports->first()->expiry_date > now() && $registry->reports->first()->expiry_date <= now()->addMonth();
        });

        $upToDateRegistries = $registries->filter(function ($registry) {
            return ! is_null($registry->reports->first()) && $registry->reports->first()->expiry_date > now();
        });

        $nonCompliantRegistries = $registries->filter(function ($registry) {
            return is_null($registry->reports->first()) || $registry->reports->first()->expiry_date < now();
        });

        $registryMetrics = $registries->count() > 0 ? ($upToDateRegistries->count() / $registries->count()) * 100 : 0;

        return Inertia::render('Workspaces/Dashboard', [
            'workspaceId' => $workspace->id,
            'nonCompliantRegistries' => RegistryResource::collection($nonCompliantRegistries->loadMissing('reports')->take(3)),
            'expiringRegistries' => RegistryResource::collection($expiringRegistries->loadMissing('reports')->take(3)),
            'upToDateRegistriesCount' => $upToDateRegistries->count(),
            'registriesCount' => $registries->count(),
            'registryMetrics' => $registryMetrics,
            'workspace' => $workspace,
        ]);
    }
}
