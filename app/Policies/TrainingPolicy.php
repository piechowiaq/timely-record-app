<?php

namespace App\Policies;

use App\Models\Training;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class TrainingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view training');
    }

    public function view(User $user, Training $training): bool
    {
        return $user->hasPermissionTo('view training');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create training');
    }

    public function update(User $user, Training $training): bool
    {
        return $user->hasPermissionTo('update training');
    }

    public function delete(User $user, Training $training): bool
    {
        return $user->hasPermissionTo('delete training');
    }

    public function restore(User $user, Training $training): bool
    {
    }

    public function forceDelete(User $user, Training $training): bool
    {
    }
}
