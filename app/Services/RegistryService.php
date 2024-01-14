<?php

namespace App\Services;

use App\Models\Registry;
use App\Models\Workspace;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class RegistryService
{
    protected RegistryRepositoryInterface $registryRepository;

    public function __construct(RegistryRepositoryInterface $registryRepository)
    {
        $this->registryRepository = $registryRepository;
    }

    public function getWorkspaceRegistriesQuery(Workspace $workspace): Builder
    {
        return $this->registryRepository->getWorkspaceRegistriesQuery($workspace);
    }

    public function applyFilters($query, Request $request): Builder
    {
        if ($request->has('search')) {
            $query->where('registries.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;
    }

    public function createRegistry(array $registryData): Registry
    {
        return Registry::create([
            'name' => $registryData['name'],
            'description' => $registryData['description'],
            'validity_period' => $registryData['validity_period'],
            'project_id' => $registryData['project_id'],
        ]);
    }

    public function updateRegistry(Registry $registry, array $registryData): Registry
    {
        $registry->update([
            'name' => $registryData['name'],
            'description' => $registryData['description'],
            'validity_period' => $registryData['validity_period'],
        ]);

        return $registry;
    }

    public function deleteRegistry(Registry $registry): void
    {
        $registry->delete();
    }
}
