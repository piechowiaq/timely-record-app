<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Http\Resources\RegistryResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Workspace::class, 'workspace');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        if (Auth::user()->isSuperAdmin()) {
            $workspaces = Workspace::query()
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();
        } else {
            $workspaces = Auth::user()->workspaces()
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();
        }

        return inertia('Workspaces/Index', [
            'workspaces' => WorkspaceResource::collection($workspaces),
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Workspaces/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $project = Project::find(session('project_id'));

        $workspace = Workspace::create([
            'name' => $request->name,
            'location' => $request->location,
            'project_id' => $project->id,
        ]);

        Auth::user()->workspaces()->attach($workspace->id);

        return to_route('workspaces.index')
            ->with('success', 'Workspace created.');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workspace $workspace)
    {
        return inertia('Workspaces/Edit', [
            'workspace' => WorkspaceResource::make($workspace),
        ]);
    }

    public function indexRegistries(Request $request, Workspace $workspace): Response
    {
        $this->authorize('update', $workspace);

        $project = Project::find(session('project_id'));

        $registriesIds = Registry::where('project_id', $project->id)
            ->orWhereNull('project_id')->pluck('id')->toArray();

        $registries = Registry::where('project_id', $project->id)
            ->orWhereNull('project_id')
            ->with('reports')
            ->with('workspaces')
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return inertia('Workspaces/IndexRegistries', [
            'workspace' => WorkspaceResource::make($workspace),
            'workspaceRegistriesIds' => $workspace->registries->pluck('id')->toArray(),
            'registries' => RegistryResource::collection($registries),
            'registriesIds' => $registriesIds,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Workspace $workspace)
    {
        $workspace->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return to_route('workspaces.edit', $workspace->id)->with('success', 'Workspace updated.');
    }

    public function syncRegistries(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $workspace->registries()->sync($request->registriesIds);

        return to_route('workspaces.index-registries', $workspace->id)->with('success', 'Workspace updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Workspace $workspace)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $workspace->delete();

        return to_route('workspaces.index')->with('success', 'Workspace deleted.');
    }
}
