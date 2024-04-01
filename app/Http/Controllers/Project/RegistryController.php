<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRegistryRequest;
use App\Http\Requests\UpdateProjectRegistryRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\RegistryResource;
use App\Models\Project;
use App\Models\Registry;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use App\Services\RegistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class RegistryController extends Controller
{
    private RegistryService $registryService;

    private RegistryRepositoryInterface $registryRepository;

    public function __construct(
        RegistryService $registryService,
        RegistryRepositoryInterface $registryRepository
    ) {
        $this->registryService = $registryService;
        $this->registryRepository = $registryRepository;
        $this->authorizeResource(Registry::class, 'registry');
    }

    public function index(Request $request): Response
    {
        $project = Project::find(session('project_id'));

        $this->authorize('manage', $project);

        if (Auth::user()->isSuperAdmin()) {
            $registries = Registry::applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::all();

        } else {
            $registries = Registry::where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->applyFilters($request)
                ->paginate(10)
                ->withQueryString();

            $projects = Project::where('id', Auth::user()->project_id)->get();
        }

        return inertia('Registries/Index', [
            'registries' => RegistryResource::collection($registries),
            'filters' => $request->all(['search', 'field', 'direction']),
            'projects' => ProjectResource::collection($projects),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Project $project): Response
    {
        return inertia('Registries/Create', [
            'project' => $project,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRegistryRequest $request)
    {
        if (session('project_id') === null) {
            Registry::create([
                'name' => $request->name,
                'description' => $request->description,
                'validity_period' => $request->validity_period,
                'project_id' => null,
            ]);
        } else {
            $project = Project::find(session('project_id'));

            Registry::create([
                'name' => $request->name,
                'description' => $request->description,
                'validity_period' => $request->validity_period,
                'project_id' => $project->id,
            ]);

        }

        return redirect()->route('registries.index')
            ->with('success', 'Custom registry created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Registry $registry)
    {
        return inertia('Registries/Show', [
            'registry' => RegistryResource::make($registry),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Registry $registry)
    {
        return inertia('Registries/Edit', [
            'registry' => RegistryResource::make($registry),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Registry $registry, UpdateProjectRegistryRequest $request)
    {
        $registry->update([
            'name' => $request->name,
            'description' => $request->description,
            'validity_period' => $request->validity_period,
        ]);

        return redirect()->route('registries.edit', $registry->id)
            ->with('success', 'Custom Registry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Registry $registry, Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $registry->delete();

        return Redirect::route('registries.index')->with('success', 'Registry deleted.');
    }
}
