<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Registry;
use App\Models\Workspace;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Services\RegistryService;
use App\Services\WorkspaceService;
use Carbon\Carbon;
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

        $outdatedRegistries = $registries->filter(function ($registry) {
            return is_null($registry->reports->sortByDesc('expiry_date')->first()) || $registry->reports->sortByDesc('expiry_date')->first() < now();
        })->sortBy('expiry_date')->take(3);

        $expiringSoonRegistries = $registries->filter(function ($registry) {

            return ! is_null($registry->reports->first()) && $registry->reports->first()->expiry_date > now() && $registry->reports->first()->expiry_date <= now()->addMonth();
        })->sortBy('expiry_date')->take(3);

        return Inertia::render('Workspaces/Dashboard', [
            'workspace' => WorkspaceResource::make($workspace->loadMissing('registries')),
            'mostOutdatedRegistries' => RegistryResource::collection($outdatedRegistries->loadMissing('reports')),
            'expiringSoonRegistries' => RegistryResource::collection($expiringSoonRegistries->loadMissing('reports')),
            'outdatedRegistries' => RegistryResource::collection($outdatedRegistries->loadMissing('reports')),
            'registriesWithValidReports' => RegistryResource::collection($workspace->registriesWithValidReports),
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
