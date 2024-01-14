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
}
