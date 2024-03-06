<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Registry;
use App\Services\ProjectService;
use Illuminate\Auth\Access\AuthorizationException;
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
    }

    /**
     * Invoke the controller.
     *
     * @throws AuthorizationException
     */
    public function __invoke(Project $project): Response
    {
        $this->authorize('view', $project);

        // Get the workspaces for the project

        //        $workspaces = $this->projectService->getUserWorkspacesWithUpToDateMetrics($project, Auth::user());

        //        $workspaces = auth()->user()->workspaces()->withCount('registries')->count();
        //        $workspaces = auth()->user()->workspaces()->withCount('registries')->get();

        //        $workspaces = auth()->user()->workspaces()->with(['registries' => function ($query) {
        //            $query->withCount('reports');
        //        }])->get();

        //        $workspaces = Workspace::find(3)->registries()->withValidReport()->count();

        $workspaceId = 4; // Example workspace ID

        $registriesWithLatestValidReport = Registry::belongsToWorkspace($workspaceId)
            ->with('latestValidReport')
            ->get();

        foreach ($registriesWithLatestValidReport as $registry) {
            $latestValidReport = $registry->latestValidReport;
            if ($latestValidReport) {
                // Do something with the latest valid report
                echo "Registry ID: {$registry->id}, Latest Valid Report ID: {$latestValidReport->id}\n";
            } else {
                // No valid report exists for this registry
                echo "Registry ID: {$registry->id} has no valid reports.\n";
            }
        }

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => $workspaces,
        ]);
    }
}
