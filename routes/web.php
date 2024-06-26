<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Project\RegistryController;
use App\Http\Controllers\Project\TrainingController;
use App\Http\Controllers\Project\UserController;
use App\Http\Controllers\Project\WorkspaceController;
use App\Http\Controllers\ProjectController as ProjectDashboardController;
use App\Http\Controllers\Workspace\RegistryController as WorkspaceRegistryController;
use App\Http\Controllers\Workspace\ReportController;
use App\Http\Controllers\WorkspaceController as WorkspaceDashboardController;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    $workspaces = Workspace::query()
        ->paginate(5)
        ->withQueryString();

    return inertia('Admin/Dashboard', [

        'workspaces' => WorkspaceResource::collection($workspaces),

    ]);
})->name('test');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/project', [ProfileController::class, 'projectUpdate'])->name('project.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'web', 'role.redirect'])->group(function () {
    Route::get('/dashboard', ProjectDashboardController::class)->name('projects.dashboard');
});

Route::middleware('auth', 'verified', 'web')->group(function () {

    Route::resource('/users', UserController::class);
    Route::get('/impersonate/{user}', [UserController::class, 'impersonate'])->name('users.impersonate');
    Route::get('/leave-impersonation', [UserController::class, 'leave'])->name('users.leave-impersonation');

    Route::resource('/registries', RegistryController::class);

    Route::resource('/trainings', TrainingController::class);

    Route::resource('/workspaces', WorkspaceController::class);
    Route::get('/workspaces/{workspace}/index-registries', [WorkspaceController::class, 'indexRegistries'])->name('workspaces.index-registries');
    Route::put('/workspaces/{workspace}/sync-registries', [WorkspaceController::class, 'syncRegistries'])->name('workspaces.sync-registries');

    Route::get('/workspaces/{workspace:id}/dashboard', [WorkspaceDashboardController::class, 'dashboard'])->name('workspaces.dashboard');

    Route::get('/workspaces/{workspace}/registries', [WorkspaceRegistryController::class, 'index'])->name('workspaces.registries.index');
    Route::get('/workspaces/{workspace}/registries/{registry}', [WorkspaceRegistryController::class, 'show'])->name('workspaces.registries.show');

    Route::get('/workspaces/{workspace}/registries/reports/create-any', [ReportController::class, 'createAny'])->name('workspaces.registries.reports.create-any');
    Route::get('/workspaces/{workspace}/registries/{registry}/reports/create', [ReportController::class, 'create'])->name('workspaces.registries.reports.create');
    Route::get('/workspaces/{workspace}/registries/{registry}/reports/{report}/edit', [ReportController::class, 'edit'])->name('workspaces.registries.reports.edit');
    Route::put('/workspaces/{workspace}/registries/{registry}/reports/{report}', [ReportController::class, 'update'])->name('workspaces.registries.reports.update');
    Route::delete('/workspaces/{workspace}/registries/{registry}/reports/{report}', [ReportController::class, 'destroy'])->name('workspaces.registries.reports.destroy');
    Route::post('/workspaces/{workspace}/registries/{registry}/reports', [ReportController::class, 'store'])->name('workspaces.registries.reports.store');
    Route::get('/workspaces/{workspace}/registries/{registry}/reports/{report}', [ReportController::class, 'show'])->name('workspaces.registries.reports.show');

});

require __DIR__.'/auth.php';
