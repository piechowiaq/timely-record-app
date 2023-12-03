<?php

namespace App\Http\Controllers;

use App\Models\Registry;
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

    public function index(Request $request): Response
    {
        $query = Registry::query();
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