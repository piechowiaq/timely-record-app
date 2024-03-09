<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @throws AuthorizationException
     */
    public function __invoke(Project $project): Response
    {
        $this->authorize('view', $project);

        return Inertia::render('Projects/Dashboard', [
            'workspaces' => WorkspaceResource::collection($project->workspaces),
        ]);
    }
}
