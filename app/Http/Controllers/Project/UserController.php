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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;

    private UserService $userService;

    private RoleRepositoryInterface $roleRepository;

    private WorkspaceRepositoryInterface $workspaceRepository;

    public function __construct(UserRepositoryInterface $userRepository, UserService $userService, RoleRepositoryInterface $roleRepository, WorkspaceRepositoryInterface $workspaceRepository)
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->roleRepository = $roleRepository;
        $this->workspaceRepository = $workspaceRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Request $request): \Inertia\Response
    {
        $paginatedUsers = $this->userRepository->getUsersByProjectWithRolesQuery($project)
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
    public function create()
    {
        $roles = $this->roleRepository->getAvailableRoles();

        // Fetch all workspaces related to the user's project
        $paginatedWorkspaces = $this->workspaceRepository
            ->getWorkspacesByProjectQuery(auth()->user()->project)
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'paginatedWorkspaces' => WorkspaceResource::collection($paginatedWorkspaces),
            'workspacesIds' => $this->workspaceRepository->getWorkspacesIds($paginatedWorkspaces),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $project = auth()->user()->project;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'project_id' => $project->id,
            'password' => Hash::make(Str::random(10)),
        ]);

        if (session('selectAllWorkspaces', false)) {
            // Get IDs of all workspaces associated with the user's project
            $workspaceIds = optional(auth()->user()->project)->workspaces->pluck('id')->toArray();

            $user->workspaces()->sync($workspaceIds);
        } else {
            // Apply the action only to the specific IDs passed from the client
            $user->workspaces()->sync($request->workspacesIds);
        }

        $user->assignRole($request->role);

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
    public function edit(Project $project, User $user)
    {
        // Fetch roles excluding 'super-admin'
        $roles = Role::whereNotIn('name', ['super-admin'])->pluck('name');

        // Fetch all workspaces related to the user's project
        $workspacesQuery = optional(auth()->user()->project)->workspaces();

        $workspacesIds = [];

        // Paginate the workspaces and use tap to get the workspace IDs
        $workspaces = $workspacesQuery->tap(function ($query) use (&$workspacesIds) {
            $workspacesIds = $query->pluck('id')->toArray();
        })->paginate(5);

        // Transform the paginated workspaces collection
        $workspaces->getCollection()->transform(function ($workspace) {
            return [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'location' => $workspace->location ?? '',
            ];
        });

        // Prepare user data for the edit form
        $userData = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $user->roles->first()->name ?? null,
            'workspacesIds' => $user->workspaces->pluck('id')->toArray(),
            'email_verified' => $user->hasVerifiedEmail(),
        ];

        return Inertia::render('Users/Edit', [
            'user' => $userData,
            'roles' => $roles,
            'paginatedWorkspaces' => $workspaces,
            'workspacesIds' => $workspacesIds, // Pass all workspace IDs from the current paginated set
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, User $user, UpdateUserRequest $request)
    {
        // Update the user
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        // Sync roles and workspaces
        $user->syncRoles($request->role);
        $user->workspaces()->sync($request->workspacesIds);

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
