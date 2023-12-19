<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Report;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceRegistryReportController extends Controller
{
    public function create(Project $project, Workspace $workspace, Request $request): Response
    {

        $registryId = $request->input('registry', null);
        $registry = $registryId ? Registry::find($registryId) : null;

        return Inertia::render('Workspaces/Registries/Reports/Create', [
            'workspace' => $workspace,
            'registries' => $workspace->registries()->get(),
            'registry' => $registry,
        ]);
    }

    public function store(StoreReportRequest $request, Project $project, Workspace $workspace)
    {
        $registry = Registry::find($request->registry_id);

        $report_date = new Carbon($request->report_date);
        $expiryDate = $report_date->addMonths($registry->validity_period)->toDateString();

        $report = new Report();
        $report->report_date = $request->report_date;
        $report->expiry_date = $expiryDate;
        $report->notes = $request->notes;
        $report->workspace_id = $request->workspace_id;
        $report->registry_id = $request->registry_id;
        $report->created_by_user_id = Auth::id();
        $report->save();

        return Redirect::route('workspace.registries.index', ['project' => $project, 'workspace' => $workspace])->with('success', 'Report uploaded.');
    }
}
