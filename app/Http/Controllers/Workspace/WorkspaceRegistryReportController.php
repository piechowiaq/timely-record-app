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
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceRegistryReportController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Report::class, 'report');
    }

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
        //        $file = $request->file('report_path');
        //        $fileName = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', now()->format('YmdHis').'_'.$project->id.'_'.$workspace->id.'_'.$registry->name.'.'.$request->file('report_path')->extension());
        //
        //        $file->storeAs($project->id.'/'.$workspace->id.'/'.$registry->id, $fileName, 'reports');

        $report_date = new Carbon($request->report_date);
        $expiryDate = $report_date->addMonths($registry->validity_period)->toDateString();

        $report = new Report();
        $report->report_date = $request->report_date;
        $report->expiry_date = $expiryDate;
        $report->report_path = 'hello';
        $report->workspace_id = $request->workspace_id;
        $report->registry_id = $request->registry_id;
        $report->project_id = $project->id;
        $report->created_by_user_id = Auth::id();
        $report->save();

        return Redirect::route('workspaces.registries.show', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry])->with('success', 'Report uploaded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {

        return Storage::disk('reports')->response($project->id.'/'.$workspace->id.'/'.$registry->id.'/'.$report->report_path);

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

    public function update(Request $request, Project $project, Workspace $workspace, Registry $registry, Report $report)
    {

        $report_date = new Carbon($request->report_date);
        $expiryDate = $report_date->addMonths($registry->validity_period)->toDateString();

        $report->report_date = $request->report_date;
        $report->expiry_date = $expiryDate;
        $report->updated_by_user_id = Auth::id();
        $report->save();

        return Redirect::route('workspace.registry.reports.edit', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry, 'report' => $report])->with('success', 'Report updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {
        Storage::disk('reports')->delete($project->id.'/'.$workspace->id.'/'.$registry->id.'/'.$report->report_path);

        $report->delete();

        return Redirect::route('workspaces.registries.show', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry])->with('success', 'Report deleted.');
    }
}
