<?php

namespace App\Policies;

use App\Models\Position;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class PositionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view position');
    }

    public function view(User $user, Position $position): bool
    {
        return $user->hasPermissionTo('view position');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create position');
    }

    public function update(User $user, Position $position): bool
    {
    }

    public function delete(User $user, Position $position): bool
    {
    }

    public function restore(User $user, Position $position): bool
    {
    }

    public function forceDelete(User $user, Position $position): bool
    {
    }
}
