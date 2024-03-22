<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class Registry extends Model
{
    use HasFactory;

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function reports(): Relation
    {
        return $this->hasMany(Report::class);
    }

    public function scopeWithValidReport($query)
    {
        return $query->whereHas('reports', function ($query) {
            $query->where('expiry_date', '>', now());
        });
    }

    public function scopeApplyFilters(Builder $query, Request $request): Builder
    {
        $search = $request->input('search');

        if ($search) {
            $query->where('registries.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;
    }

    protected $fillable = [
        'name',
        'description',
        'validity_period',
        'project_id',

    ];

    public function getLatestValidReportForWorkspace($workspaceId)
    {
        return Report::where('registry_id', $this->id)
            ->where('workspace_id', $workspaceId)
            ->where('expiry_date', '>', now())
            ->latest('expiry_date')
            ->first();
    }

    public function getMostCurrentReport($workspaceId): Report
    {
        return Report::where('registry_id', $this->id)
            ->where('workspace_id', $workspaceId)
            ->where('expiry_date')
            ->latest('expiry_date')
            ->first();
    }

    public function scopeBelongsToWorkspace($query, $workspaceId)
    {
        return $query->whereHas('workspaces', function (Builder $query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        });
    }

    public function scopeValid(Builder $query)
    {
        return $query->whereHas('reports', function (Builder $query) {
            $query->where('expiry_date', '>', now());
        });
    }
}
