<?php

namespace App\Policies;

use App\Models\Training;
use AppModels\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Training $training): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Training $training): bool
    {
    }

    public function delete(User $user, Training $training): bool
    {
    }

    public function restore(User $user, Training $training): bool
    {
    }

    public function forceDelete(User $user, Training $training): bool
    {
    }
}
