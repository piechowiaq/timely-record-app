<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Models\Workspace;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class EloquentRegistryRepository implements RegistryRepositoryInterface
{
    protected function baseRegistryQuery(Workspace $workspace): Builder
    {
        return DB::table('registries')
            ->join('registry_workspace', 'registries.id', '=', 'registry_workspace.registry_id')
            ->leftJoin(DB::raw('(
            SELECT registry_id, workspace_id, MAX(expiry_date) as max_expiry_date
            FROM reports
            GROUP BY registry_id, workspace_id
        ) as max_reports'), function ($join) {
                $join->on('registry_workspace.registry_id', '=', 'max_reports.registry_id')
                    ->on('registry_workspace.workspace_id', '=', 'max_reports.workspace_id');
            })
            ->where('registry_workspace.workspace_id', '=', $workspace->id)

            ->select(
                'registry_workspace.registry_id',
                'registries.name',
                'max_reports.max_expiry_date as expiry_date',
                'registry_workspace.workspace_id'
            )
            ->groupBy(
                'registry_workspace.registry_id',
                'registries.name',
                'registry_workspace.workspace_id',
                'max_reports.max_expiry_date'
            );
    }

    public function getWorkspaceRegistriesQuery(Workspace $workspace): Builder
    {
        return $this->baseRegistryQuery($workspace);
    }

    public function getRegistriesByProjectQuery(Project $project): HasMany
    {
        return $project->registries();
    }
}
