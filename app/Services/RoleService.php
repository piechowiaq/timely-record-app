<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleService
{
    protected RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
}
