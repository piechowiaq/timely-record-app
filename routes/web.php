<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Project\RegistryController;
use App\Http\Controllers\Project\UserController;
use App\Http\Controllers\Project\WorkspaceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Workspace\WorkspaceRegistryController;
use App\Http\Controllers\Workspace\WorkspaceRegistryReportController;
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

Route::get('test', function () {
    $workspaces = Workspace::query()
        ->paginate(5)
        ->withQueryString();

    return inertia('Test', [

        'user' => WorkspaceResource::collection($workspaces),

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
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'web', 'role.redirect'])->group(function () {
    Route::get('/dashboard', ProjectController::class)->name('projects.dashboard');
});

Route::middleware('auth', 'verified', 'web')->group(function () {

    Route::resource('users', UserController::class);

    Route::resource('registries', RegistryController::class);

    Route::resource('workspaces', WorkspaceController::class);
    Route::get('workspaces/{workspace}/index-registries', [WorkspaceController::class, 'indexRegistries'])->name('workspaces.index-registries');
    Route::put('workspaces/{workspace}/sync-registries', [WorkspaceController::class, 'syncRegistries'])->name('workspaces.sync-registries');

    Route::get('projects/{project}/workspaces/{workspace:id}/dashboard', [WorkspaceController::class, 'dashboard'])->name('workspaces.dashboard');

    Route::get('/projects/{project}/workspaces/{workspace}/registries', [WorkspaceRegistryController::class, 'index'])->name('workspace.registries.index');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/{registry}', [WorkspaceRegistryController::class, 'show'])->name('workspace.registries.show');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/reports/create', [WorkspaceRegistryReportController::class, 'create'])->name('workspace.registry.reports.create');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}/edit', [WorkspaceRegistryReportController::class, 'edit'])->name('workspace.registry.reports.edit');
    Route::patch('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}', [WorkspaceRegistryReportController::class, 'update'])->name('workspace.registry.reports.update');
    Route::delete('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}', [WorkspaceRegistryReportController::class, 'destroy'])->name('workspace.registry.reports.destroy');
    Route::post('/projects/{project}/workspaces/{workspace}/registries/reports', [WorkspaceRegistryReportController::class, 'store'])->name('workspace.registry.reports.store');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}', [WorkspaceRegistryReportController::class, 'show'])->name('workspace.registry.reports.show');

});

require __DIR__.'/auth.php';
