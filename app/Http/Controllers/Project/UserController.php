<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    private RoleRepositoryInterface $roleRepository;

    private WorkspaceRepositoryInterface $workspaceRepository;

    private UserService $userService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserService $userService,
        RoleRepositoryInterface $roleRepository,
        WorkspaceRepositoryInterface $workspaceRepository)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->roleRepository = $roleRepository;
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, UserRepositoryInterface $userRepository, Request $request): \Inertia\Response
    {
        $paginatedUsers = $userRepository->getUsersByProjectWithRolesQuery($project)
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'paginatedUsers' => UserResource::collection($paginatedUsers),
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RoleRepositoryInterface $roleRepository, WorkspaceRepositoryInterface $workspaceRepository, Request $request): \Inertia\Response
    {
        $roles = $roleRepository->getAvailableRoles();

        // Fetch all workspaces related to the user's project
        $paginatedWorkspaces = $workspaceRepository
            ->getWorkspacesByProjectQuery(auth()->user()->project)
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'paginatedWorkspaces' => WorkspaceResource::collection($paginatedWorkspaces),
            'workspacesIds' => $workspaceRepository->getWorkspacesIds($paginatedWorkspaces),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, Project $project): RedirectResponse
    {
        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'project_id' => $project->id,
            'workspacesIds' => $request->workspacesIds,
            'role' => $request->role,
        ];

        $this->userService->createUser($userData);

        return redirect()->route('users.index', ['project' => $project])
            ->with('success', 'User created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, User $user, WorkspaceRepositoryInterface $workspaceRepository, UserRepositoryInterface $userRepository)
    {
        // Fetch roles excluding 'super-admin'
        $roles = $this->roleRepository->getAvailableRoles();

        $allWorkspacesIds = $workspaceRepository->getWorkspacesByProjectIds($project);

        $paginatedWorkspaces = $workspaceRepository
            ->getWorkspacesByProjectQuery($project)
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Users/Edit', [
            'user' => UserResource::make($user),
            'roles' => $roles,
            'paginatedWorkspaces' => $paginatedWorkspaces,
            'allWorkspacesIds' => $allWorkspacesIds, // Pass all workspace IDs from the current project
            'workspacesIds' => $workspaceRepository->getWorkspacesIds($paginatedWorkspaces), // Pass all workspace IDs from the current paginated set
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, User $user, UpdateUserRequest $request, UserService $userService): RedirectResponse
    {
        // Define the user data to be updated
        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'workspacesIds' => $request->workspacesIds,
            'role' => $request->role,
        ];

        // Update the user using the UserService
        $userService->updateUser($user, $userData);

        // Redirect back with success message
        return redirect()->route('users.edit', ['project' => $project, 'user' => $user])
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, User $user, Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user->delete();

        return Redirect::route('users.index', ['project' => $project])->with('success', 'User deleted.');

    }
}
