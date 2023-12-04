<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Workspace;
use App\Services\RegistryService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    private RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Request $request)
    {
        $query = Workspace::where('project_id', $project->id);

        $filteredQuery = $this->applyFilters($query, $request);

        $paginatedWorkspaces = $filteredQuery->paginate(10)->withQueryString();

        return Inertia::render('Workspaces/Index', [
            'paginatedWorkspaces' => $paginatedWorkspaces,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    public function applyFilters($query, Request $request): Builder
    {
        if ($request->has('search')) {
            $query->where('workspaces.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $project = Auth::user()->project;

        return Inertia::render('Workspaces/Create', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkspaceRequest $request)
    {
        $project = Auth::user()->project;

        $validated = $request->validated();

        $validated['project_id'] = $project->id;

        $workspace = Workspace::create($validated);

        // Associating the authenticated user with the created workspace
        Auth::user()->workspaces()->attach($workspace->id);

        // Determine if this is the user's only workspace
        $redirectToDashboard = Auth::user()->workspaces()->count() === 1;

        // Redirect to the appropriate route
        if ($redirectToDashboard) {
            // If it's their first workspace, redirect to the workspace dashboard
            return redirect()->route('workspaces.dashboard', ['project' => $project->id, 'workspace' => $workspace->id])
                ->with('success', 'Workspace created.');
        } else {
            // Handle redirection for users who already have other workspaces, if needed
            return redirect()->route('workspaces.index', ['project' => $project])
                ->with('success', 'Workspace created.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Project $project, Workspace $workspace)
    {
        $query = Registry::query();
        $filteredQuery = $this->registryService->applyFilters($query, $request);

        $paginatedRegistries = $filteredQuery->paginate(10)->withQueryString();

        return Inertia::render('Workspaces/Edit', [
            'workspace' => $workspace,
            'paginatedRegistries' => $paginatedRegistries,
            'filters' => $request->all(['search', 'field', 'direction']),
            'registriesIds' => $workspace->registries->pluck('id')->toArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Project $project, Workspace $workspace)
    {
        $validated = $request->validated();

        $workspace->update($validated);

        return redirect()->route('workspaces.edit', ['project' => $project, 'workspace' => $workspace])->with('success', 'Workspace updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Project $project, Workspace $workspace)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $workspace->delete();

        // Redirect to the projects.show route
        return redirect()->route('projects.show', $project)->with('success', 'Workspace deleted.');
    }

    /**
     * Show the dashboard for the specified resource.
     */
    public function dashboard(Project $project, Workspace $workspace)
    {
        return Inertia::render('Workspaces/Dashboard', [
            'project' => $project,
            'workspace' => $workspace,
        ]);
    }
}
