<?php

namespace App\Http\Controllers;

use App\Models\Project;
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

        $workspaces = $this->projectService->getWorkspacesWithUpToDateMetrics($project);

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => $workspaces,
        ]);
    }
}
