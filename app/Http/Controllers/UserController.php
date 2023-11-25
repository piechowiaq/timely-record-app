<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, Request $request)
    {
        $search = $request->input('search');
        $sortField = $request->input('field'); // the field to sort by
        $sortDirection = $request->input('direction'); // the direction of sorting

        $users = User::where('project_id', $project->id)
            ->where('id', '<>', auth()->id())
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('roles', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($sortField, function ($query) use ($sortField, $sortDirection) {

                $sortDirection = $sortDirection ?? 'asc';

                return $query->orderBy($sortField, $sortDirection);
            })
            ->with('roles')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'role' => $user->roles->first()->name ?? null,
                    'email' => $user->email,
                    'email_verified' => $user->hasVerifiedEmail(),
                ];
            });

        return Inertia::render('Users/Index', [
            'paginatedUsers' => $users,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->pluck('name');

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

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'paginatedWorkspaces' => $workspaces,
            'workspacesIds' => $workspacesIds, // Pass all workspace IDs from the current paginated set
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
