<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Workspace;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Services\RegistryService;
use App\Services\WorkspaceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    private RegistryService $registryService;

    private WorkspaceRepositoryInterface $workspaceRepository;

    private WorkspaceService $workspaceService;

    private RegistryRepositoryInterface $registryRepository;

    public function __construct(
        RegistryService $registryService,
        WorkspaceRepositoryInterface $workspaceRepository,
        WorkspaceService $workspaceService,
        RegistryRepositoryInterface $registryRepository
    ) {
        $this->registryService = $registryService;
        $this->workspaceRepository = $workspaceRepository;
        $this->workspaceService = $workspaceService;
        $this->registryRepository = $registryRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Request $request)
    {
        $paginatedWorkspaces = $this->workspaceRepository->getWorkspacesByProjectQuery($project)
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Workspaces/Index', [
            'paginatedWorkspaces' => $paginatedWorkspaces,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Workspaces/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request, Project $project)
    {

        $workspaceData = $request->only('name', 'location');
        $workspaceData['project_id'] = $project->id;

        $workspace = $this->workspaceService->createWorkspace($workspaceData);

        // Determine if this is the user's only workspace
        $redirectToDashboard = Auth::user()->workspaces()->count() === 1;

        // Redirect to the appropriate route
        if ($redirectToDashboard) {
            // If it's their first workspace, redirect to the workspace dashboard
            return redirect()->route('workspaces.dashboard', ['project' => $project->id, 'workspace' => $workspace->id])
                ->with('success', 'Workspace created.');
        } else {
            // Handle redirection for users who already have other workspaces, if needed
            return redirect()->route('workspaces.index', ['project' => $project])
                ->with('success', 'Workspace created.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Workspace $workspace)
    {
        return Inertia::render('Workspaces/Edit', [
            'workspace' => $workspace,
        ]);
    }

    public function editRegistries(Request $request, Project $project, Workspace $workspace): \Inertia\Response
    {
        //Fetch all registries generic and custom to project
        $paginatedRegistries = $this->registryRepository->getRegistriesByProjectQuery($project)
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        $allRegistriesIds = $this->registryRepository->getRegistriesByProjectIds($project);

        return Inertia::render('Workspaces/EditRegistries', [
            'workspace' => $workspace,
            'paginatedRegistries' => $paginatedRegistries,
            'workspaceRegistriesIds' => $workspace->registries->pluck('id'),
            'filters' => $request->all(['search', 'field', 'direction']),
            'allRegistriesIds' => $allRegistriesIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Project $project, Workspace $workspace)
    {
        $this->workspaceService->updateWorkspace($workspace, $request->only('name', 'location'));

        return redirect()->route('workspaces.edit', ['project' => $project, 'workspace' => $workspace])->with('success', 'Workspace updated.');
    }

    public function syncRegistries(Request $request, Project $project, Workspace $workspace): \Illuminate\Http\RedirectResponse
    {
        // Get the registry IDs from the request
        $registriesIds = $request->registriesIds ?? [];

        // Use the service to sync the registries
        $this->workspaceService->syncRegistries($workspace, $registriesIds);

        return redirect()->route('workspaces.edit-registries', ['project' => $project, 'workspace' => $workspace])->with('success', 'Workspace updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project, Workspace $workspace)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $this->workspaceService->deleteWorkspace($workspace);

        // Redirect to the projects.show route
        return redirect()->route('workspaces.index', $project)->with('success', 'Workspace deleted.');
    }

    /**
     * Show the dashboard for the specified resource.
     */
    public function dashboard(Project $project, Workspace $workspace)
    {
        $regis = Registry::whereHas('workspaces', function ($query) use ($workspace) {
            $query->where('workspaces.id', $workspace->id);
        })
            ->with(['reports' => function ($query) {
                $query->select('registry_id', 'workspace_id', DB::raw('MAX(expiry_date) as max_expiry_date'))
                    ->groupBy('registry_id', 'workspace_id');
            }])
            ->get()
            ->map(function ($registry) {
                // Assuming the 'reports' relationship will return a collection, even if it's empty
                $latestReport = $registry->reports->first();

                return [
                    'registry_id' => $registry->id,
                    'name' => $registry->name,
                    'expiry_date' => $latestReport ? $latestReport->max_expiry_date : null,
                    'workspace_id' => $latestReport ? $latestReport->workspace_id : null,
                ];
            });

        $mostOutdatedRegistries = $this->getMostOutdatedRegistries($workspace, 3);
        $recentlyUpdatedRegistries = $this->getRecentlyUpdatedRegistries($workspace, 3);
        $percentageOfUpToDate = $this->getPercentageOfUpToDate($workspace);
        $countOfUpToDateRegistries = $this->countOfUpToDateRegistries($workspace);
        $countOfExpiredRegistries = $this->countOfExpiredRegistries($workspace);
        $expiringSoonRegistries = $this->getExpiringSoonRegistries($workspace, 3);

        return Inertia::render('Workspaces/Dashboard', [
            'project' => $project,
            'workspace' => $workspace,
            'mostOutdatedRegistries' => $mostOutdatedRegistries,
            'recentlyUpdatedRegistries' => $recentlyUpdatedRegistries,
            'percentageOfUpToDate' => $percentageOfUpToDate,
            'countOfUpToDateRegistries' => $countOfUpToDateRegistries,
            'countOfExpiredRegistries' => $countOfExpiredRegistries,
            'expiringSoonRegistries' => $expiringSoonRegistries,
        ]);
    }

    protected function baseRegistryQuery(Workspace $workspace): \Illuminate\Database\Query\Builder
    {
        return DB::table('registries')
            ->join('registry_workspace', 'registries.id', '=', 'registry_workspace.registry_id')
            ->leftJoin(DB::raw('(
        SELECT registry_id, workspace_id, MAX(expiry_date) as max_expiry_date
        FROM reports
        GROUP BY registry_id, workspace_id
    ) as max_reports'), function ($join) {
                $join->on('registry_workspace.registry_id', '=', 'max_reports.registry_id')
                    ->on('registry_workspace.workspace_id', '=', 'max_reports.workspace_id');
            })
            ->where('registry_workspace.workspace_id', '=', $workspace->id)

            ->select(
                'registry_workspace.registry_id',
                'registries.name',
                'max_reports.max_expiry_date as expiry_date',
                'registry_workspace.workspace_id'
            )
            ->groupBy(
                'registry_workspace.registry_id',
                'registries.name',
                'registry_workspace.workspace_id',
                'max_reports.max_expiry_date'
            );
    }

    public function getMostOutdatedRegistries(Workspace $workspace, int $limit): array
    {
        $registries = $this->baseRegistryQuery($workspace)
            ->where(function ($query) {
                $query->whereNull('max_reports.max_expiry_date')
                    ->orWhere('max_reports.max_expiry_date', '<', Carbon::now());
            })
            ->orderBy('max_reports.max_expiry_date', 'asc') // This will put NULL expiry_dates (i.e., no reports) at the top.
            ->limit($limit);

        return $registries->get()->map(function ($registry) {
            return [
                'name' => $registry->name,
                'registry_id' => $registry->registry_id,
                'workspace_id' => $registry->workspace_id,
            ];
        })->all();
    }

    public function getRecentlyUpdatedRegistries(Workspace $workspace, int $limit): array
    {
        $query = $this->baseRegistryQuery($workspace)
            ->whereNotNull('max_reports.max_expiry_date')
            ->where('max_reports.max_expiry_date', '>', Carbon::now())
            ->orderBy('max_reports.max_expiry_date', 'desc')
            ->limit($limit);

        return $query->get()->map(function ($registry) {
            return [
                'name' => $registry->name,
                'registry_id' => $registry->registry_id,
                'workspace_id' => $registry->workspace_id,
            ];
        })->all();
    }

    public function getUpToDateRegistries(Workspace $workspace): array
    {
        $results = $this->baseRegistryQuery($workspace)->get();

        return $results->where('expiry_date', '>', Carbon::now())->toArray();

    }

    public function getExpiredRegistries(workspace $workspace): array
    {
        $query = $this->baseRegistryQuery($workspace)
            ->where(function ($query) {
                $query->where('max_reports.max_expiry_date', '<', Carbon::now())
                    ->orWhereNull('max_reports.max_expiry_date');
            });

        $results = $query->get();

        return $results->toArray();
    }

    public function getExpiringSoonRegistries(Workspace $workspace, int $limit): array
    {
        $today = Carbon::now();
        $endOfRange = $today->copy()->addDays(30); // 30 days from today

        $query = $this->baseRegistryQuery($workspace)
            ->whereBetween('max_reports.max_expiry_date', [$today, $endOfRange])
            ->orderBy('max_reports.max_expiry_date', 'asc')->limit($limit); // This will order registries by their expiry date, starting with the ones expiring soonest.

        return $query->get()->map(function ($registry) {
            return [
                'name' => $registry->name,
                'registry_id' => $registry->registry_id,
                'workspace_id' => $registry->workspace_id,
                'expiry_date' => $registry->expiry_date,
            ];
        })->all();
    }

    public function countOfUpToDateRegistries(Workspace $workspace): int
    {
        return count($this->getUpToDateRegistries($workspace));
    }

    public function countOfExpiredRegistries(Workspace $workspace): int
    {
        return count($this->getExpiredRegistries($workspace));
    }

    public function getPercentageOfUpToDate(Workspace $workspace): float
    {
        $countOfUpToDateRegistries = count($this->getUpToDateRegistries($workspace));
        $countOfExpiredRegistries = count($this->getExpiredRegistries($workspace));

        $totalRegistries = $countOfUpToDateRegistries + $countOfExpiredRegistries;

        if ($totalRegistries == 0) {
            return 0.0; // or any other default value you'd like to return when there's no registry
        }

        return round(($countOfUpToDateRegistries / $totalRegistries) * 100);
    }
}
