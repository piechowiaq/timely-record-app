<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Role;
use App\Models\Scopes\ProjectScope;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct()
    {
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

        if (Auth::user()->isSuperAdmin()) {
            $users = User::with('roles', 'workspaces')
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::all();
        } else {
            $users = User::inWorkspaces($authUserWorkspacesIds)
                ->with('roles')
                ->with('workspaces')
                ->withRolesEligibleToView(auth()->user()->getRoleNames()->first())
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::where('id', Auth::user()->project_id)->get();
        }

        return inertia('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->all(['search', 'field', 'direction', 'projectId']),
            'projects' => ProjectResource::collection($projects),
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

        $user->workspaces()->sync($request->workspacesIds);
        $user->assignRole($request->role);

        return to_route('users.edit', $user->id)
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
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        $user->workspaces()->sync($request->workspacesIds);
        $user->syncRoles($request->role);

        return to_route('users.edit', $user->id)
            ->with('success', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user->hasRole('project-admin') || $user->hasRole('super-admin') ? $user->delete() : $user->forceDelete();

        return to_route('users.index')->with('success', 'User deleted.');
    }

    public function impersonate(User $user)
    {

        if (! is_null(auth()->user()->project_id)) {
            return;
        }

        $originalId = auth()->user()->id;
        session()->put('impersonate', $originalId);
        auth()->loginUsingId($user->id);

        return to_route('projects.dashboard');
    }

    public function leave()
    {
        if (! session()->has('impersonate')) {
            abort(403);
        }

        auth()->login(User::withoutGlobalScope(ProjectScope::class)->find(session('impersonate')));
        session()->forget('impersonate');

        return to_route('projects.dashboard');
    }
}
