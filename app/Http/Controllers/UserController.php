<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $users = $project->users->filter(function ($user) {
            return $user->id !== auth()->id();
        });

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->pluck('name');

        // Get the user's project and then the associated workspaces
        //        $workspaces = optional(auth()->user()->project)->workspaces()->get() ?? collect();
        //        $workspaces = optional(auth()->user()->project)->workspaces()->pluck('name')->values()->toArray() ?? [];

        // Get the user's project and then the associated workspaces
        $workspaces = optional(auth()->user()->project)->workspaces->map(function ($workspace) {
            return [
                'id' => $workspace->id,
                'name' => $workspace->name,
            ];
        }) ?? collect();

        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'workspaces' => $workspaces,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'role' => 'required|exists:roles,name',
            'workspacesIds' => 'required|array',
            'workspacesIds.*' => 'required|exists:workspaces,id',

        ]);

        $project = auth()->user()->project;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'project_id' => $project->id,
            'password' => Hash::make(Str::random(10)),
        ]);

        $user->workspaces()->sync($request->workspacesIds);

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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
