<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Models\Workspace;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;

class EloquentWorkspaceRepository implements WorkspaceRepositoryInterface
{
    public function getWorkspacesByProject(Project $project)
    {
        return Workspace::where('project_id', $project->id)->get();
    }
}
