<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Invoke the controller.
     *
     * @throws AuthorizationException
     */
    public function __invoke(): Response
    {
        $project = Project::find(session('project_id'));

        $this->authorize('view', $project);

        return inertia('Projects/Dashboard', [
            'workspaces' => WorkspaceResource::collection(Auth::user()->workspaces),
        ]);
    }
}
