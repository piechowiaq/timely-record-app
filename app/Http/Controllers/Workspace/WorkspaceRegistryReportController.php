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

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $file->storeAs('reports', $filename, 's3');
        }

        $file = Storage::get('reports/'.$filename);

        Storage::put('reports/'.$filename, $file);

        //        return Storage::disk('s3')->response('reports/'.$filename);
        //        return Storage::disk('s3')->url('reports/'.$filename);

        // Getting image Storage::disk('reports')->get($path)
        // Getting url Storage::disk('reports')->url($path) https://timelyrecord-private.s3.eu-central-1.amazonaws.com/reports/AzT6ON2FaozjXOR870oYeHjfDrFeHSVKtZfbKcfv.jpg
        // Getting streamed response Storage::disk('reports')->response($path)

        $report_date = new Carbon($request->report_date);
        $expiryDate = $report_date->addMonths($registry->validity_period)->toDateString();

        $report = new Report();
        $report->report_date = $request->report_date;
        $report->expiry_date = $expiryDate;
        $report->filename = $filename;
        $report->url = Storage::disk('s3')->url($filename);
        $report->extension = $request->file('file')->extension();
        $report->workspace_id = $request->workspace_id;
        $report->registry_id = $request->registry_id;
        $report->project_id = $project->id;
        $report->created_by_user_id = Auth::id();
        $report->save();

        return Redirect::route('workspace.registries.show', ['project' => $project, 'workspace' => $workspace, 'registry' => $registry])->with('success', 'Report uploaded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {

        return Storage::disk('s3')->response('reports/'.$report->filename);

        //        return Storage::disk('s3')->response('reports/'.$report->filename);
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
        $expiryDate = $report_date->addMonths($registry->validity_period)->toDateString();

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
