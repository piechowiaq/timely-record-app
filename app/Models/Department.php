<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'project_id',
    ];

    protected function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class);
    }

    public function scopeApplyFilters(Builder $query, Request $request): Builder
    {
        $search = $request->input('search');

        if ($search) {
            $query->where('departments.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        if ($request->has('projectId')) {
            $query->where('project_id', $request->get('projectId'));
        }

        return $query;
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}
