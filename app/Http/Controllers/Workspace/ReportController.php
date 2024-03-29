<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Resources\RegistryResource;
use App\Http\Resources\ReportResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Project;
use App\Models\Registry;
use App\Models\Report;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Report::class, 'report');
    }

    public function createAny(Workspace $workspace): Response
    {
        $this->authorize('createAny', [Report::class, $workspace]);

        return inertia('Workspaces/Registries/Reports/CreateAny', [
            'workspace' => WorkspaceResource::make($workspace),
            'registries' => RegistryResource::collection($workspace->registries),
        ]);
    }

    public function create(Workspace $workspace, Registry $registry): Response
    {
        $this->authorize('createAny', [Report::class, $workspace]);

        return inertia('Workspaces/Registries/Reports/Create', [
            'workspace' => WorkspaceResource::make($workspace),
            'registry' => RegistryResource::make($registry),

        ]);
    }

    public function store(Workspace $workspace, Registry $registry, StoreReportRequest $request)
    {
        $project = Project::find(session('project_id'));

        $reportDate = new Carbon($request->report_date);
        $expiryDate = (clone $reportDate)->addMonths($registry->validity_period);

        $file = $request->file('report_path');
        $date = Carbon::now()->format('m-d-Y_H-i-s');

        $fileName = preg_replace('/[^A-Za-z0-9\-_.]/', '_', $date.'_'.$project->id.'_'.$workspace->id.'_'.$registry->name.'.'.$file->extension());
        $file->storeAs($project->id.'/'.$workspace->id.'/'.$registry->id, $fileName, 'reports');

        Report::create([
            'report_date' => $reportDate,
            'expiry_date' => $expiryDate,
            'report_path' => $fileName,
            'created_by_user_id' => Auth::id(),
            'project_id' => $project->id,
            'workspace_id' => $workspace->id,
            'registry_id' => $registry->id,

        ]);

        return to_route('workspaces.registries.show', [$workspace->id, $registry->id])->with('success', 'Report uploaded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workspace $workspace, Registry $registry, Report $report)
    {
        $project = Project::find(session('project_id'));

        return Storage::disk('reports')->response($project->id.'/'.$workspace->id.'/'.$registry->id.'/'.$report->report_path);
    }

    public function edit(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {
        $this->authorize('view', $report);

        return inertia('Workspaces/Registries/Reports/Edit', [
            'report' => ReportResource::make($report),
            'workspace' => WorkspaceResource::make($workspace),
            'registry' => RegistryResource::make($registry),
        ]);
    }

    public function update(Request $request, Workspace $workspace, Registry $registry, Report $report)
    {

        $report_date = new Carbon($request->report_date);
        $expiryDate = $report_date->addMonths($registry->validity_period);

        $report->report_date = $request->report_date;
        $report->expiry_date = $expiryDate;
        $report->updated_by_user_id = Auth::id();
        $report->save();

        return to_route('workspaces.registries.reports.edit', [$workspace->id, $registry->id, $report->id])->with('success', 'Report updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Workspace $workspace, Registry $registry, Report $report)
    {
        Storage::disk('reports')->delete($project->id.'/'.$workspace->id.'/'.$registry->id.'/'.$report->report_path);

        $report->delete();

        return to_route('workspaces.registries.show', [$workspace->id, $registry->id])->with('success', 'Report deleted.');
    }
}
