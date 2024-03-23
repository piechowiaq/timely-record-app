<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegistryResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Registry;
use App\Models\Workspace;
use App\Services\RegistryService;
use Illuminate\Http\Request;
use Inertia\Response;

class RegistryController extends Controller
{
    protected RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    public function index(Request $request, Workspace $workspace): Response
    {
        $this->authorize('view', $workspace);

        $registriesQuery = $this->registryService->getRegistriesWithLatestReportQuery($workspace->id);

        $registries = $registriesQuery->with('reports')
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return inertia('Workspaces/Registries/Index', [
            'registries' => RegistryResource::collection($registries),
            'filters' => $request->all(['search', 'field', 'direction']),
            'workspace' => $workspace,
        ]);

    }

    public function show(Request $request, Workspace $workspace, Registry $registry)
    {
        $this->authorize('view', $workspace);

        $currentReport = $registry->reports()
            ->where('workspace_id', $workspace->id)
            ->latest('expiry_date')
            ->first();

        $otherReports = $registry->reports()
            ->where('workspace_id', $workspace->id)
            ->when($currentReport, function ($query) use ($currentReport) {
                return $query->where('id', '!=', $currentReport->id);
            })->applyFilters($request)->get();

        return inertia('Workspaces/Registries/Show', [
            'workspace' => WorkspaceResource::make($workspace),
            'registry' => RegistryResource::make($registry),
            'currentReport' => $currentReport ? ReportResource::make($currentReport) : null,
            'otherReports' => $otherReports ? ReportResource::collection($otherReports) : null,
            'filters' => $request->all(['field', 'direction']),

        ]);
    }
}
