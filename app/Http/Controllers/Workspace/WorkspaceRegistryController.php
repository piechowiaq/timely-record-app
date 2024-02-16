<?php

namespace App\Http\Controllers\Workspace;

use App\Models\Project;
use App\Models\Registry;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WorkspaceRegistryController
{
    public function index(Request $request, Project $project, Workspace $workspace): \Inertia\Response
    {
        $this->authorize('view', $project);

        $query = DB::table('registries')
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

        if ($request->has('search')) {
            $query->where('registries.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return Inertia::render('Workspaces/Registries/Index', [
            'paginatedRegistries' => $query->paginate(10)
                ->withQueryString(),
            'filters' => $request->all(['search', 'field', 'direction']),
            'workspace' => $workspace,
        ]);

    }

    public function show(Request $request, Project $project, Workspace $workspace, Registry $registry)
    {
        $mostCurrentReport = $this->getMostCurrentReport($registry, $workspace);
        $reports = $this->getReports($registry, $workspace);
        $historicalReports = $this->getHistoricalReports($reports, $mostCurrentReport);

        if ($request->has(['field', 'direction'])) {
            $field = $request->get('field');
            $direction = $request->get('direction');
            $historicalReports = $this->sortHistoricalReports($historicalReports, $field, $direction);
        }

        return Inertia::render('Workspaces/Registries/Show', [
            'workspace' => $workspace,
            'registry' => $registry,
            'filters' => $request->all(['field', 'direction']),
            'historicalReports' => $historicalReports,
            'mostCurrentReport' => $mostCurrentReport,
            'reports' => $reports,
        ]);
    }

    public function getAllRegistriesQuery(workspace $workspace): \Illuminate\Database\Query\Builder
    {
        return $this->getAllRegistriesQuery($workspace);
    }

    public function getMostOutdatedRegistries(workspace $workspace, int $limit): array
    {
        return $this->getMostOutdatedRegistries($workspace, $limit);
    }

    public function getRecentlyUpdatedRegistries(workspace $workspace, int $limit): array
    {
        return $this->getRecentlyUpdatedRegistries($workspace, $limit);
    }

    public function getExpiringSoonRegistries(workspace $workspace, int $limit): array
    {
        return $this->getExpiringSoonRegistries($workspace, $limit);
    }

    public function getPercentageOfUpToDate(workspace $workspace): float
    {
        $countOfUpToDateRegistries = count($this->getUpToDateRegistries($workspace));
        $countOfExpiredRegistries = count($this->getExpiredRegistries($workspace));

        $totalRegistries = $countOfUpToDateRegistries + $countOfExpiredRegistries;

        if ($totalRegistries == 0) {
            return 0.0; // or any other default value you'd like to return when there's no registry
        }

        return round(($countOfUpToDateRegistries / $totalRegistries) * 100);
    }

    public function countOfUpToDateRegistries(workspace $workspace): int
    {
        return $this->countOfUpToDateRegistries($workspace);
    }

    public function countOfExpiredRegistries(workspace $workspace): int
    {
        return $this->countOfExpiredRegistries($workspace);
    }

    public function applyFilters($query, Request $request): \Illuminate\Database\Query\Builder
    {
        if ($request->has('search')) {
            $query->where('registries.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;
    }

    public function getReports(Registry $registry, Workspace $workspace): array
    {
        return $registry->reports()
            ->with(['updatedByUser', 'createdByUser'])
            ->where('workspace_id', $workspace->id)
            ->get()
            ->toArray();
    }

    public function getMostCurrentReport(Registry $registry, Workspace $workspace): \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\Relation|null
    {
        return $registry->reports()
            ->with(['updatedByUser', 'createdByUser'])
            ->where('workspace_id', $workspace->id)
            ->latest('expiry_date')
            ->first();
    }

    public function getHistoricalReports(array $reports, $mostCurrentReport): array
    {
        return array_filter($reports, function ($report) use ($mostCurrentReport) {
            return $report['id'] !== $mostCurrentReport->id;
        });
    }

    public function sortHistoricalReports(array &$historicalReports, string $field, string $direction): array
    {
        usort($historicalReports, function ($a, $b) use ($field, $direction) {
            return $direction === 'asc' ? $a[$field] <=> $b[$field] : $b[$field] <=> $a[$field];
        });

        return array_values($historicalReports);
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
}
