<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
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

        return Redirect::route('workspace.registries.show', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry])->with('success', 'Report uploaded.');
    }

    public function edit(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {
        return Inertia::render('Workspaces/Registries/Reports/Edit', [
            'report' => $report,
            'workspace' => $workspace,
            'registry' => $registry,
            'project' => $project,
        ]);
    }

    public function update(UpdateReportRequest $request, Project $project, Workspace $workspace, Registry $registry, Report $report)
    {
        $report_date = new Carbon($request->report_date);
        $expiryDate = $report_date->addMonths($registry->valid_for)->toDateString();

        $report->report_date = $request->report_date;
        $report->expiry_date = $expiryDate;
        $report->notes = $request->notes;
        $report->workspace_id = $request->workspace_id;
        $report->registry_id = $request->registry_id;
        $report->updated_by_user_id = Auth::id();
        $report->save();

        return Redirect::route('workspace.registry.reports.edit', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry, 'report' => $report])->with('success', 'Report updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {

        $report->delete();

        return Redirect::route('workspace.registries.show', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry])->with('success', 'Report deleted.');
    }
}
