<?php

namespace App\Models;

use App\Models\Scopes\ProjectScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class Workspace extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'project_id',
    ];

    /**
     * Get the project that owns the workspace.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The users that belong to the workspace.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The registries that belong to the workspace.
     */
    public function registries(): BelongsToMany
    {
        return $this->belongsToMany(Registry::class);
    }

    public function scopeApplyFilters(Builder $query, Request $request): Builder
    {
        $search = $request->input('search');

        if ($search) {
            $query->where('workspaces.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;

    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ProjectScope);
    }

    /**
     * Get the registry metrics for the workspace.
     */
    public function registryMetrics(): float|int
    {
        $registries = $this->registries()->with(['reports' => function ($query) {
            $query->validWorkspaceRegistry($this->id);
        }])->get();

        $registriesWithValidReport = $registries->filter(function ($registry) {
            return $registry->reports->isNotEmpty();
        });

        $registryMetrics = $registries->count() > 0 ? ($registriesWithValidReport->count() / $registries->count()) * 100 : 0;

        return round($registryMetrics);
    }

    public function registriesWithValidReports(): Builder|BelongsToMany|\LaravelIdea\Helper\App\Models\_IH_Registry_QB
    {
        $workspaceId = $this->id;

        return $this->registries()
            ->whereHas('reports', function ($query) use ($workspaceId) {
                $query->where('expiry_date', '>', now())
                    ->where('workspace_id', $workspaceId);
            });
    }
}
