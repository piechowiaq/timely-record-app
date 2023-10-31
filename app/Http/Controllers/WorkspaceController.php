<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        //        return redirect()->route('projects.show', $project)->with('success', 'Workspace created.');
        return redirect()->route('workspaces.dashboard', ['project' => $project, 'workspace' => $workspace])->with('success', 'Workspace created.');

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
    public function edit(Project $project, Workspace $workspace)
    {
        return Inertia::render('Workspaces/Edit', [
            'workspace' => $workspace,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Project $project, Workspace $workspace)
    {
        $validated = $request->validated();

        $workspace->update($validated);

        return redirect()->route('workspaces.edit', [
            'project' => $project->id,
            'workspace' => $workspace->id,
        ])->with('success', 'Workspace updated.');
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
