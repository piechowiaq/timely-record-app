<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function scopeEligibleToAssign(Builder $query, string $userRole): void
    {
        if ($userRole === 'project-admin') {
            $query->whereNotIn('name', ['project-admin', 'super-admin']);
        } else {
            $query->whereNotIn('name', ['admin', 'project-admin', 'super-admin']);
        }
    }
}
