<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRegistryRequest;
use App\Models\Project;
use App\Models\Registry;
use App\Services\RegistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProjectRegistryController extends Controller
{
    private RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    public function index(Request $request, Project $project): Response
    {

        $query = Registry::query();

        // Include registries with null project_id and those associated with the given project
        $query->where(function ($query) use ($project) {
            $query->whereNull('project_id')
                ->orWhere('project_id', $project->id);
        });
        $filteredQuery = $this->registryService->applyFilters($query, $request);

        $paginatedRegistries = $filteredQuery->paginate(10)->withQueryString();

        return Inertia::render('Registries/Index', [
            'paginatedRegistries' => $filteredQuery->paginate(10)->withQueryString(),
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Registries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRegistryRequest $request)
    {
        $registry = new Registry();
        $registry->name = $request->get('name');
        $registry->description = $request->get('description');
        $registry->validity_period = $request->get('validity_period');
        $registry->project_id = $request->get('projectId');
        $registry->save();

        return Redirect::route('project.registries.index', ['project' => $request->get('projectId')]);
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
