<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\UserRepositoryInterface;

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

    public function getFilteredUsers($projectId, $search, $sortField, $sortDirection, $excludeUserId)
    {
        $query = $this->userRepository->getSearchedUsers($search);

        if ($sortField) {
            $query = $this->userRepository->sortUsersByField($query, $sortField, $sortDirection);
        }

        $query->where('project_id', $projectId)
            ->where('users.id', '<>', $excludeUserId)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'project-admin');
            });

        return $query->with('roles')->paginate(10);
    }
}
