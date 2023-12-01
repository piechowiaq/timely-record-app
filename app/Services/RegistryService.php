<?php

namespace App\Services;

use App\Models\Workspace;
use App\Repositories\Contracts\RegistryRepositoryInterface;
use Illuminate\Http\Request;

class RegistryService
{
    protected RegistryRepositoryInterface $registryRepository;

    public function __construct(RegistryRepositoryInterface $registryRepository)
    {
        $this->registryRepository = $registryRepository;
    }

    public function getWorkspaceRegistriesQuery(Workspace $workspace): \Illuminate\Database\Query\Builder
    {
        return $this->registryRepository->getWorkspaceRegistriesQuery($workspace);
    }

    public function applyFilters($query, Request $request): \Illuminate\Database\Query\Builder
    {
        if ($request->has('search')) {
            $query->where('registries.name', 'like', '%'.$request->get('search').'%');
        }

        if ($request->has(['field', 'direction'])) {
            $query->orderBy($request->get('field'), $request->get('direction'));
        }

        return $query;
    }
}
