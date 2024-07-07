<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Department;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Department::class, 'department');
    }

    public function index(Request $request)
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        if (Auth::user()->isSuperAdmin()) {
            $departments = Department::applyFilters($request)
                ->whereNull('project_id')
                ->paginate(10)
                ->withQueryString();

            $projects = Project::all();

        } else {
            $departments = Department::where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::where('id', Auth::user()->project_id)->get();
        }

        return inertia('Projects/Departments/Index', [
            'departments' => DepartmentResource::collection($departments),
            'filters' => $request->all(['search', 'field', 'direction']),
            'projects' => ProjectResource::collection($projects),
        ]);

    }

    public function create()
    {
        if (Auth::user()->isSuperAdmin()) {
            $workspacesIds = [];
            $workspaces = collect();
        } else {
            $workspacesIds = Auth::user()->workspaces->pluck('id')->toArray();

            if (Auth::user()->hasRole('project-admin')) {
                // If the user is a project-admin, get all project workspaces
                $projectWorkspacesIds = Auth::user()->project->workspaces->pluck('id')->toArray();
                $workspacesIds = array_merge($workspacesIds, $projectWorkspacesIds);
            }

            $workspaces = Workspace::whereIn('id', $workspacesIds)
                ->paginate(5)
                ->withQueryString();
        }

        return inertia('Projects/Departments/Create', [
            'workspaces' => WorkspaceResource::collection($workspaces),
            'workspacesIds' => $workspacesIds,
        ]);
    }

    public function store(DepartmentRequest $request)
    {
        if (session('project_id') === null) {
            Department::create([
                'name' => $request->name,
                'project_id' => null,
            ]);
        } else {
            $project = Project::find(session('project_id'));

            $department = Department::create([
                'name' => $request->name,
                'project_id' => $project->id,
            ]);

            $department->workspaces()->sync($request->workspacesIds);

        }

        return redirect()->route('departments.index')
            ->with('success', 'Department created.');
    }

    public function show(Department $department)
    {
        return inertia('Projects/Departments/Show', [
            'department' => DepartmentResource::make($department),
        ]);
    }

    public function edit(Department $department)
    {
        $authUserWorkspacesIds = auth()->user()->workspaces->pluck('id')->toArray();

        $workspaces = Workspace::whereIn('id', $authUserWorkspacesIds)
            ->paginate(5)
            ->withQueryString();

        $department->load('workspaces');

        return inertia('Projects/Departments/Edit', [
            'department' => DepartmentResource::make($department),
            'workspaces' => WorkspaceResource::collection($workspaces),
            'workspacesIds' => $authUserWorkspacesIds,
        ]);
    }

    public function update(Department $department, DepartmentRequest $request)
    {
        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->route('departments.edit', $department->id)
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department, Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $department->delete();

        return to_route('departments.index')->with('success', 'Department deleted.');
    }
}
