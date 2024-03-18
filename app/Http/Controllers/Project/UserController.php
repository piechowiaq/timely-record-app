<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WorkspaceRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
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

        $authUserWorkspacesIds = auth()->user()->workspaces->pluck('id')->toArray();

        $users = User::inWorkspaces($authUserWorkspacesIds)
            ->with('roles')
            ->with('workspaces')
            ->withRolesEligibleToView(auth()->user()->getRoleNames()->first())
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return inertia('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $roles = Role::eligibleToAssign(auth()->user()->getRoleNames()->first())->get();

        $workspacesIds = auth()->user()->workspaces->pluck('id')->toArray();

        $workspaces = Workspace::whereIn('id', $workspacesIds)
            ->paginate(5)
            ->withQueryString();

        return inertia('Users/Create', [
            'roles' => RoleResource::collection($roles),
            'workspaces' => WorkspaceResource::collection($workspaces),
            'workspacesIds' => $workspacesIds,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $project = Project::find(session('project_id'));

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(10)),
            'project_id' => $project->id,
        ]);

        $user->project()->associate($project);
        $user->workspaces()->sync($request->workspacesIds);
        $user->assignRole($request->role);

        return to_route('users.index')
            ->with('success', 'User created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {

        $roles = Role::eligibleToAssign(auth()->user()->getRoleNames()->first())->get();

        $workspacesIds = auth()->user()->workspaces->pluck('id')->toArray();

        $workspaces = Workspace::whereIn('id', $workspacesIds)
            ->paginate(5)
            ->withQueryString();

        return inertia('Users/Edit', [
            'user' => UserResource::make($user),
            'roles' => RoleResource::collection($roles),
            'workspaces' => WorkspaceResource::collection($workspaces),
            'workspacesIds' => $workspacesIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, UpdateUserRequest $request): RedirectResponse
    {
        $userData = $request->only('first_name', 'last_name', 'email', 'workspacesIds', 'role');

        // Update the user using the UserService
        $this->userService->updateUser($user, $userData);

        // Redirect back with success message
        return to_route('users.edit', $user->id)
            ->with('success', 'User updated.');
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
