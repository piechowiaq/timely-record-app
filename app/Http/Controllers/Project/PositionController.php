<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\PositionResource;
use App\Http\Resources\ProjectResource;
use App\Models\Department;
use App\Models\Position;
use App\Models\Project;
use Auth;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Position::class, 'position');
    }

    public function index(Request $request)
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        if (Auth::user()->isSuperAdmin()) {
            $positions = Position::applyFilters($request)
                ->with('department')
                ->paginate(10)
                ->withQueryString();

            $projects = Project::all();

        } else {
            $positions = Position::where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->with('department')
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::where('id', Auth::user()->project_id)->get();
        }

        return inertia('Projects/Positions/Index', [
            'positions' => PositionResource::collection($positions),
            'filters' => $request->all(['search', 'field', 'direction']),
            'projects' => ProjectResource::collection($projects),
        ]);

    }

    public function create()
    {
        $project = Project::find(session('project_id'));

        $departments = Auth::user()->isSuperAdmin()
            ? Department::all()
            : Department::where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->get();

        return inertia('Projects/Positions/Create', [
            'departments' => $departments ? DepartmentResource::collection($departments) : [],
        ]);
    }

    public function store(PositionRequest $request)
    {
        if (session('project_id') === null) {
            Position::create([
                'name' => $request->name,
                'project_id' => null,
                'department_id' => $request->departmentId,
            ]);
        } else {
            $project = Project::find(session('project_id'));

            Position::create([
                'name' => $request->name,
                'project_id' => $project->id,
                'department_id' => $request->departmentId,
            ]);

        }

        return redirect()->route('positions.index')
            ->with('success', 'Position created.');
    }

    public function show(Position $position)
    {
        return inertia('Projects/Positions/Show', [
            'position' => PositionResource::make($position),
        ]);
    }

    public function edit(Position $position)
    {
        return inertia('Projects/Positions/Edit', [
            'position' => PositionResource::make($position),
        ]);
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
