<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function getAvailableRoles()
    {
        return Role::whereNotIn('name', ['super-admin', 'project-admin'])
            ->pluck('name');
    }
}
