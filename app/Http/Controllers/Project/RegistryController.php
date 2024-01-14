<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRegistryRequest;
use App\Http\Requests\UpdateProjectRegistryRequest;
use App\Models\Project;
use App\Models\Registry;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use App\Services\RegistryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
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
    }

    public function index(Request $request, Project $project): Response
    {
        $paginatedRegistries = $this->registryRepository->getRegistriesByProjectQuery($project)
            ->applyFilters($request)
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Registries/Index', [
            'paginatedRegistries' => $paginatedRegistries,
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
    public function store(StoreProjectRegistryRequest $request, Project $project)
    {
        $registryData = $request->only('name', 'description', 'validity_period');
        $registryData['project_id'] = $project->id;

        $this->registryService->createRegistry($registryData);

        return redirect()->route('registries.index', ['project' => $project])
            ->with('success', 'User created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Registry $registry)
    {
        return Inertia::render('Registries/Show', [
            'registry' => $registry,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project, Registry $registry)
    {
        return Inertia::render('Registries/Edit', [
            'registry' => $registry,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Project $project, Registry $registry, UpdateProjectRegistryRequest $request)
    {
        $userRegistry = $request->only('name', 'description', 'validity_period');

        $this->registryService->updateRegistry($registry, $userRegistry);

        return redirect()->route('registries.edit', ['project' => $project->id, 'registry' => $registry->id])
            ->with('success', 'Custom Registry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Registry $registry, Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $this->registryService->deleteRegistry($registry);

        return Redirect::route('registries.index', ['project' => $project])->with('success', 'Registry deleted.');
    }
}
