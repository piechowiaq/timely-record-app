<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Workspace;
use App\Services\RegistryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistryController extends Controller
{
    private RegistryService $registryService;

    public function __construct(RegistryService $registryService)
    {
        $this->registryService = $registryService;
    }

    public function index(Request $request, Project $project, Workspace $workspace): Response
    {

        $registryQuery = $this->registryService->getWorkspaceRegistriesQuery($workspace);
        $filteredRegistries = $this->registryService->applyFilters($registryQuery, $request);

        return Inertia::render('Registries/Index', [
            'paginatedRegistries' => $filteredRegistries->paginate(10)
                ->withQueryString(),
            'filters' => $request->all(['search', 'field', 'direction']),
            'workspace' => $workspace,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
