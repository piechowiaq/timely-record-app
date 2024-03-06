<?php

namespace App\Services;

use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

class WorkspaceService
{
    public function createWorkspace(array $workspaceData): Workspace
    {
        $workspace = Workspace::create([
            'name' => $workspaceData['name'],
            'location' => $workspaceData['location'],
            'project_id' => $workspaceData['project_id'],
        ]);

        Auth::user()->workspaces()->attach($workspace->id);

        return $workspace;
    }

    public function updateWorkspace(Workspace $workspace, array $workspaceData): Workspace
    {
        $workspace->update([
            'name' => $workspaceData['name'],
            'location' => $workspaceData['location'],
        ]);

        return $workspace;
    }

    public function deleteWorkspace(Workspace $workspace): void
    {
        $workspace->delete();
    }

    public function syncRegistries(Workspace $workspace, array $registriesIds): void
    {
        $workspace->registries()->sync($registriesIds);
    }
}
