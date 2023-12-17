<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Workspace;
use App\Services\RegistryService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    private RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Request $request)
    {
        $query = Workspace::where('project_id', $project->id);

        $filteredQuery = $this->applyFilters($query, $request);

        $paginatedWorkspaces = $filteredQuery->paginate(10)->withQueryString();

        return Inertia::render('Workspaces/Index', [
            'paginatedWorkspaces' => $paginatedWorkspaces,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    public function applyFilters($query, Request $request): Builder
    {
        if ($request->has('search')) {
            $query->where('workspaces.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = Auth::user()->project;

        return Inertia::render('Workspaces/Create', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $project = Auth::user()->project;

        $validated = $request->validated();

        $validated['project_id'] = $project->id;

        $workspace = Workspace::create($validated);

        // Associating the authenticated user with the created workspace
        Auth::user()->workspaces()->attach($workspace->id);

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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project, Workspace $workspace)
    {

        return Inertia::render('Workspaces/Edit', [
            'workspace' => $workspace,
        ]);
    }

    public function editRegistries(Request $request, Project $project, Workspace $workspace): \Inertia\Response
    {
        $registriesQuery = Registry::query()
            ->where(function ($query) use ($project) {
                $query->whereNull('project_id')
                    ->orWhere('project_id', $project->id);
            });

        // Apply filters to the query before executing it
        $filteredRegistriesQuery = $this->registryService->applyFilters($registriesQuery, $request);

        // Fetch the IDs of the filtered registries
        $registriesIds = $filteredRegistriesQuery->pluck('id')->toArray();

        // Paginate the filtered registries
        $paginatedRegistries = $filteredRegistriesQuery->paginate(10)->withQueryString();

        return Inertia::render('Workspaces/EditRegistries', [
            'workspace' => $workspace,
            'paginatedRegistries' => $paginatedRegistries,
            'workspaceRegistries' => $workspace->registries->pluck('id')->toArray(),
            'filters' => $request->all(['search', 'field', 'direction']),
            'registriesIds' => $registriesIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Project $project, Workspace $workspace)
    {

        $validated = $request->validated();

        $workspace->update($validated);

        return redirect()->route('workspaces.edit', ['project' => $project, 'workspace' => $workspace])->with('success', 'Workspace updated.');
    }

    public function syncRegistries(Request $request, Project $project, Workspace $workspace): \Illuminate\Http\RedirectResponse
    {
        $registriesIds = $request->registriesIds ?? [];

        $workspace->registries()->sync($registriesIds);

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

        $workspace->delete();

        // Redirect to the projects.show route
        return redirect()->route('projects.show', $project)->with('success', 'Workspace deleted.');
    }

    /**
     * Show the dashboard for the specified resource.
     */
    public function dashboard(Project $project, Workspace $workspace)
    {
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

    public function getAllRegistriesQuery(Workspace $workspace): \Illuminate\Database\Query\Builder
    {
        return $this->baseRegistryQuery($workspace);
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
