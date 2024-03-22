<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Workspace::class, 'workspace');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        $workspaces = Auth::user()->workspaces()
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return inertia('Workspaces/Index', [
            'workspaces' => WorkspaceResource::collection($workspaces),
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Workspaces/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $project = Project::find(session('project_id'));

        $workspace = Workspace::create([
            'name' => $request->name,
            'location' => $request->location,
            'project_id' => $project->id,
        ]);

        Auth::user()->workspaces()->attach($workspace->id);

        return to_route('workspaces.index')
            ->with('success', 'Workspace created.');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workspace $workspace)
    {
        return inertia('Workspaces/Edit', [
            'workspace' => WorkspaceResource::make($workspace),
        ]);
    }

    public function indexRegistries(Request $request, Workspace $workspace): Response
    {
        $this->authorize('update', $workspace);

        $project = Project::find(session('project_id'));

        $registriesIds = Registry::where('project_id', $project->id)
            ->orWhereNull('project_id')->pluck('id')->toArray();

        $registries = Registry::where('project_id', $project->id)
            ->orWhereNull('project_id')
            ->with('workspaces')
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return inertia('Workspaces/IndexRegistries', [
            'workspace' => WorkspaceResource::make($workspace),
            'registries' => RegistryResource::collection($registries),
            'registriesIds' => $registriesIds,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Workspace $workspace)
    {
        $workspace->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return to_route('workspaces.edit', $workspace->id)->with('success', 'Workspace updated.');
    }

    public function syncRegistries(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $workspace->registries()->sync($request->registriesIds);

        return to_route('workspaces.index-registries', $workspace->id)->with('success', 'Workspace updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Workspace $workspace)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $workspace->delete();

        return to_route('workspaces.index')->with('success', 'Workspace deleted.');
    }

    /**
     * Show the dashboard for the specified resource.
     */
    public function dashboard(Project $project, Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $hasRegistries = Registry::whereHas('workspaces', function ($query) use ($workspace) {
            $query->where('workspaces.id', $workspace->id);
        })->exists();

        Registry::whereHas('workspaces', function ($query) use ($workspace) {
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
            'hasRegistries' => $hasRegistries,
            'percentageOfUpToDate' => $percentageOfUpToDate,
            'countOfUpToDateRegistries' => $countOfUpToDateRegistries,
            'countOfExpiredRegistries' => $countOfExpiredRegistries,
            'expiringSoonRegistries' => $expiringSoonRegistries,
            'canViewProject' => auth()->user()->can('view', $project),
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
