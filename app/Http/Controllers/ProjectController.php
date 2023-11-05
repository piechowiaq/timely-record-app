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
     * Display the specified resource.
     *
     * @throws AuthorizationException
     */
    public function dashboard(Project $project): Response
    {
        $this->authorize('view', $project);

        $workspaces = $project->workspaces;

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => $workspaces,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        $workspaces = $project->workspaces;

        return Inertia::render('Projects/Show', [
            'workspaces' => $workspaces,
        ]);
    }
}
