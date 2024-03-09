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
use Inertia\Response;

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
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        $users = User::with('roles')
            ->where('project_id', $project->id)
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', '=', 'project-admin');
            })
            ->where('id', '<>', auth()->id())
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RoleRepositoryInterface $roleRepository, Request $request, Project $project): Response
    {
        $roles = $roleRepository->getAvailableRoles();

        // Fetch all workspaces related to the user's project
        $paginatedWorkspaces = $this->workspaceRepository
            ->getWorkspacesByProjectQuery(auth()->user()->project)
            ->paginate(5)
            ->withQueryString();

        $project = $request->user()->project;

        $allWorkspacesIds = $this->workspaceRepository->getWorkspacesByProjectIds($project);

        return inertia('Users/Create', [
            'roles' => $roles,
            'workspaces' => WorkspaceResource::collection($paginatedWorkspaces),
            'workspacesIds' => $this->workspaceRepository->getWorkspacesIds($paginatedWorkspaces),
            'allWorkspacesIds' => $allWorkspacesIds,
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, Project $project): RedirectResponse
    {
        $userData = $request->only('first_name', 'last_name', 'email', 'workspacesIds', 'role');
        $userData['project_id'] = $project->id;

        $this->userService->createUser($userData);

        return redirect()->route('users.index', ['project' => $project])
            ->with('success', 'User created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, User $user): Response
    {
        // Fetch roles excluding 'super-admin'
        $roles = $this->roleRepository->getAvailableRoles();

        $allWorkspacesIds = $this->workspaceRepository->getWorkspacesByProjectIds($project);

        $paginatedWorkspaces = $this->workspaceRepository
            ->getWorkspacesByProjectQuery($project)
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Users/Edit', [
            'user' => UserResource::make($user),
            'roles' => $roles,
            'workspaces' => WorkspaceResource::collection($paginatedWorkspaces),
            'allWorkspacesIds' => $allWorkspacesIds, // Pass all workspace IDs from the current project
            'workspacesIds' => $this->workspaceRepository->getWorkspacesIdsByUser($user), // Pass all workspace IDs from the current paginated set
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, User $user, UpdateUserRequest $request): RedirectResponse
    {
        $userData = $request->only('first_name', 'last_name', 'email', 'workspacesIds', 'role');

        // Update the user using the UserService
        $this->userService->updateUser($user, $userData);

        // Redirect back with success message
        return redirect()->route('users.edit', ['project' => $project, 'user' => $user])
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, User $user, Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $this->userService->deleteUser($user);

        return Redirect::route('users.index', ['project' => $project])->with('success', 'User deleted.');
    }
}
