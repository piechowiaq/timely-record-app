<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;

class EloquentWorkspaceRepository implements WorkspaceRepositoryInterface
{
    public function getWorkspacesByProjectQuery(Project $project)
    {
        return Workspace::where('project_id', $project->id);
    }

    //    public function getUserWorkspacesByProjectQuery(Project $project, User $user)
    //    {
    //        return Workspace::where('project_id', $project->id)->whereHas('users', function ($query) use ($user) {
    //            $query->where('users.id', $user->id);
    //        });
    //    }
    //
    public function getWorkspacesByProjectIds(Project $project)
    {
        return $this->getWorkspacesByProjectQuery($project)->pluck('id')->toArray();
    }

    public function getWorkspacesIds($workspaces)
    {
        return $workspaces->pluck('id')->toArray();
    }

    //
    //    protected function baseRegistryQuery(Workspace $workspace): \Illuminate\Database\Query\Builder
    //    {
    //        return DB::table('registries')
    //            ->join('registry_workspace', 'registries.id', '=', 'registry_workspace.registry_id')
    //            ->leftJoin(DB::raw('(
    //            SELECT registry_id, workspace_id, MAX(expiry_date) as max_expiry_date
    //            FROM reports
    //            GROUP BY registry_id, workspace_id
    //        ) as max_reports'), function ($join) {
    //                $join->on('registry_workspace.registry_id', '=', 'max_reports.registry_id')
    //                    ->on('registry_workspace.workspace_id', '=', 'max_reports.workspace_id');
    //            })
    //            ->where('registry_workspace.workspace_id', '=', $workspace->id)
    //
    //            ->select(
    //                'registry_workspace.registry_id',
    //                'registries.name',
    //                'max_reports.max_expiry_date as expiry_date',
    //                'registry_workspace.workspace_id'
    //            )
    //            ->groupBy(
    //                'registry_workspace.registry_id',
    //                'registries.name',
    //                'registry_workspace.workspace_id',
    //                'max_reports.max_expiry_date'
    //            );
    //    }
    //
    //    public function getUpToDateRegistries(Workspace $workspace): array
    //    {
    //        $results = $this->baseRegistryQuery($workspace)->get();
    //
    //        return $results->where('expiry_date', '>', Carbon::now())->toArray();
    //
    //    }
    //
    //    public function getExpiredRegistries(Workspace $workspace): array
    //    {
    //        $query = $this->baseRegistryQuery($workspace)
    //            ->where(function ($query) {
    //                $query->where('max_reports.max_expiry_date', '<', Carbon::now())
    //                    ->orWhereNull('max_reports.max_expiry_date');
    //            });
    //
    //        $results = $query->get();
    //
    //        return $results->toArray();
    //    }
    //
    public function getWorkspacesIdsByUser(User $user)
    {
        return $user->workspaces->pluck('id') ?? collect();
    }
}
