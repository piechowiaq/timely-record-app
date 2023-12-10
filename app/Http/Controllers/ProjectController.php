<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
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

        $workspaces = $project->workspaces;

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => $workspaces,
        ]);
    }
}
