<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Resources\PositionResource;
use App\Http\Resources\ProjectResource;
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
                ->paginate(10)
                ->withQueryString();

            $projects = Project::all();

        } else {
            $positions = Position::where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::where('id', Auth::user()->project_id)->get();
        }

        return inertia('Projects/Positions/Index', [
            'positions' => PositionResource::collection($positions->with('department')),
            'filters' => $request->all(['search', 'field', 'direction']),
            'projects' => ProjectResource::collection($projects),
        ]);

    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
