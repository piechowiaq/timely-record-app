<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createUser(array $userData)
    {
        // Create a new user with the provided data
        $user = User::create([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'project_id' => $userData['project_id'],
            'password' => Hash::make(Str::random(10)),
        ]);

        // Sync the user's workspaces
        $user->workspaces()->sync($userData['workspacesIds']);

        // Assign the specified role to the user
        $user->assignRole($userData['role']);

        return $user;
    }
}
