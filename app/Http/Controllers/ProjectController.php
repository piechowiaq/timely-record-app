<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Services\RegistryService;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Response;

class ProjectController extends Controller
{
    protected RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    /**
     * Invoke the controller.
     *
     * @throws AuthorizationException
     */
    public function __invoke(): Response
    {
        $project = Project::find(session('project_id'));

        $this->authorize('view', $project);

        $workspaces = Auth::user()->workspaces->each(function ($workspace) {
            $workspace->registryMetrics = $this->registryService->getRegistryMetrics(
                $this->registryService->getRegistriesWithLatestReport($workspace->id)
            );
        });

        return inertia('Projects/Dashboard', [
            'workspaces' => WorkspaceResource::collection($workspaces),
        ]);
    }
}
