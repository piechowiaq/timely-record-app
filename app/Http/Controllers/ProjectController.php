<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function dashboard(Project $project): \Inertia\Response
    {
        dd($project->workspaces);
        $workspaces = $project->workspaces;

        return Inertia::render('Projects/Show', [
            'workspaces' => $workspaces,
        ]);
    }
}
