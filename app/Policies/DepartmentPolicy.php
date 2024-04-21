<?php

namespace App\Policies;

use App\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view department');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create department');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('update department');
    }
}
