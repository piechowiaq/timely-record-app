<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProjectResource;
use App\Models\Department;
use App\Models\Project;
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
        return inertia('Projects/Departments/Create');
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

            Department::create([
                'name' => $request->name,
                'project_id' => $project->id,
            ]);

        }

        return redirect()->route('departments.index')
            ->with('success', 'Department created.');
    }

    public function show($id)
    {
    }

    public function edit(Department $department)
    {
        return inertia('Projects/Departments/Edit', [
            'department' => DepartmentResource::make($department),
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

    public function destroy($id)
    {
    }
}
