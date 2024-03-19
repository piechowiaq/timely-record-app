<?php

namespace App\Policies;

use App\Models\Registry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view registry');
    }

    public function view(User $user, Registry $registry): bool
    {
        return $user->hasPermissionTo('view registry');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create registry');
    }

    public function update(User $user, Registry $registry): bool
    {
        return $user->hasPermissionTo('update registry');
    }

    public function delete(User $user, Registry $registry): bool
    {
        return $user->hasPermissionTo('delete registry');
    }

    public function restore(User $user, Registry $registry): bool
    {
    }

    public function forceDelete(User $user, Registry $registry): bool
    {
        return $user->hasPermissionTo('delete user');
    }
}
