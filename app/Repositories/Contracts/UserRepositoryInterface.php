<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface UserRepositoryInterface
{
    public function getSearchedUsers(string $searchInput);

    public function sortUsersByField(Builder $query, string $field, string $direction);
}
