<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
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

        return redirect()->route('projects.show', $project)->with('success', 'Workspace created.');

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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequest $request, Workspace $workspace)
    {
        $validated = $request->validated();

        $workspace = Workspace::findOrFail($workspace);
        $workspace->update($validated);

        dd('Where to redirect?');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
