<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TrainingResource;
use App\Models\Project;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        if (Auth::user()->isSuperAdmin()) {
            $trainings = Training::applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::all();

        } else {
            $trainings = Training::where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::where('id', Auth::user()->project_id)->get();
        }

        return inertia('Projects/Trainings/Index', [
            'trainings' => TrainingResource::collection($trainings),
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
