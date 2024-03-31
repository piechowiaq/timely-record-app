<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\User;
use App\Models\Workspace;
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

        if (Auth::user()->isSuperAdmin()) {
            $workspaces = Workspace::all()->each(function ($workspace) {
                $workspace->registryMetrics = $this->registryService->getRegistryMetrics(
                    $this->registryService->getRegistriesWithLatestReport($workspace->id)
                );
            });
            $customRegistriesCount = Registry::whereNotNull('project_id')->count();
        } else {
            $workspaces = Auth::user()->workspaces->each(function ($workspace) {
                $workspace->registryMetrics = $this->registryService->getRegistryMetrics(
                    $this->registryService->getRegistriesWithLatestReport($workspace->id)
                );
            });
            $customRegistriesCount = Registry::where('project_id', $project->id)->count();
        }

        return inertia('Projects/Dashboard', [
            'workspaces' => WorkspaceResource::collection($workspaces),
            'projectsCount' => Project::count(),
            'workspacesCount' => Workspace::count(),
            'genericRegistriesCount' => Registry::whereNull('project_id')->count(),
            'customRegistriesCount' => $customRegistriesCount,
            'usersCount' => User::count(),
        ]);
    }
}
