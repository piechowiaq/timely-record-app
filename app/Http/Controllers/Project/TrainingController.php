<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectTrainingRequest;
use App\Http\Requests\UpdateProjectTrainingRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TrainingResource;
use App\Models\Project;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Training::class, 'training');
    }

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
        return inertia('Projects/Trainings/Create');
    }

    public function store(StoreProjectTrainingRequest $request)
    {
        if (session('project_id') === null) {
            Training::create([
                'name' => $request->name,
                'description' => $request->description,
                'validity_period' => $request->validity_period,
                'project_id' => null,
            ]);
        } else {
            $project = Project::find(session('project_id'));

            Training::create([
                'name' => $request->name,
                'description' => $request->description,
                'validity_period' => $request->validity_period,
                'project_id' => $project->id,
            ]);

        }

        return redirect()->route('trainings.index')
            ->with('success', 'Custom training created.');
    }

    public function show(Training $training)
    {
        return inertia('Projects/Trainings/Show', [
            'training' => TrainingResource::make($training),
        ]);
    }

    public function edit(Training $training)
    {
        return inertia('Projects/Trainings/Edit', [
            'training' => TrainingResource::make($training),
        ]);
    }

    public function update(Training $training, UpdateProjectTrainingRequest $request)
    {
        $training->update([
            'name' => $request->name,
            'description' => $request->description,
            'validity_period' => $request->validity_period,
        ]);

        return redirect()->route('trainings.edit', $training->id)
            ->with('success', 'Custom Training updated successfully.');
    }

    public function destroy(Training $training, Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $training->delete();

        return to_route('trainings.index')->with('success', 'Training deleted.');
    }
}
