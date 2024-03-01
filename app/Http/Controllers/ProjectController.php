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
        $this->authorizeResource(Project::class, 'project');
    }

    /**
     * Invoke the controller.
     *
     * @throws AuthorizationException
     */
    public function __invoke(Project $project): Response
    {

        $workspaces = $this->projectService->getWorkspacesWithUpToDateMetrics($project);

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => $workspaces,
            'canCreateWorkspace' => auth()->user()->can('createWorkspace', $project),

        ]);
    }
}
