<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getUsersByProjectWithRoles(Project $project): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $project->users()->where('users.id', '<>', auth()->id())
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'project-admin');
            })
            ->with('roles');
    }

    public function applyUserFilters($query, Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('field'); // the field to sort by
        $sortDirection = $request->input('direction') ?? 'asc';

        if ($request->has('search')) {
            $query->where('first_name', 'like', '%'.$search.'%')
                ->orWhere('last_name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')
                ->orWhereHas('roles', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                });
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }
}
