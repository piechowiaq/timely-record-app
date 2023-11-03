<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function dashboard(Project $project): Response
    {
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
