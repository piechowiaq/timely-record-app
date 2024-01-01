<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Workspace;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    /**
     * Create the controller instance.
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Invoke the controller.
     *
     * @throws AuthorizationException
     */
    public function __invoke(Project $project): Response
    {
        $this->authorize('view', $project);

        $workspaces = $this->projectService->getWorkspaces($project)->map(function ($workspace) {
            $workspace->percentageOfUpToDate = $this->getPercentageOfUpToDate($workspace);

            return $workspace;
        });

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => $workspaces,
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
