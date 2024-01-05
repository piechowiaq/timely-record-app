<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function getSearchedUsers(?string $searchInput): Builder
    {
        $query = User::query();

        if ($searchInput) {
            $query->where('first_name', 'like', "%{$searchInput}%")
                ->orWhere('last_name', 'like', "%{$searchInput}%")
                ->orWhere('email', 'like', "%{$searchInput}%")
                ->orWhereHas('roles', function ($query) use ($searchInput) {
                    $query->where('name', 'like', "%{$searchInput}%");
                });
        }

        return $query;
    }

    public function sortUsersByField(Builder $query, ?string $field, ?string $direction): void
    {
        if ($field === 'role') {
            // Adjust this line based on your database structure
            $query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $direction)
                ->select('users.*'); // Avoid selecting columns from joined tables
        } else {
            $query->orderBy($field, $direction);
        }
    }
}
