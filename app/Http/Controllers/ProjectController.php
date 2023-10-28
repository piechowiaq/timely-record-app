<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $workspaces = $project->workspaces;

        return Inertia::render('Projects/Show', [
            'workspaces' => $workspaces,
        ]);
    }
}
