<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsersByProject(Project $project, string $search = null, string $sortField = null, string $sortDirection = 'asc'): Collection
    {
        $query = $this->userRepository->getSearchedUsers($search);
        $query = $this->userRepository->sortUsersByField($query, $sortField, $sortDirection);

        // Additional filters like by project
        $query->where('project_id', $project->id);

        return $query->get();
    }
}
