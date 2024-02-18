<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Project\RegistryController;
use App\Http\Controllers\Project\UserController;
use App\Http\Controllers\Project\WorkspaceController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Workspace\WorkspaceRegistryController;
use App\Http\Controllers\Workspace\WorkspaceRegistryReportController;
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
    $registries = \App\Models\Registry::paginate(10)->withQueryString();

    $allRegistriesIds = \App\Models\Registry::all()->pluck('id');

    $workspace = \App\Models\Workspace::findOrFail(1);

    return Inertia::render('Test', [
        'paginatedRegistries' => $registries,
        'allRegistriesIds' => $allRegistriesIds,
        'workspaceRegistriesIds' => $workspace->registries->pluck('id'),
    ]);
});

Route::post('test', function (Illuminate\Http\Request $request) {
    dd($request);
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

Route::middleware('auth', 'verified', 'web', 'access.control')->group(function () {

    Route::get('/projects/{project}/dashboard', ProjectController::class)->name('projects.dashboard');

    //will need to group these routes by project
    Route::get('projects/{project}/users', [UserController::class, 'index'])->name('users.index');
    Route::get('projects/{project}/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('projects/{project}/users', [UserController::class, 'store'])->name('users.store');
    Route::get('projects/{project}/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('projects/{project}/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('projects/{project}/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('projects/{project}/registries', [RegistryController::class, 'index'])->name('registries.index');
    Route::get('projects/{project}/create', [RegistryController::class, 'create'])->name('registries.create');
    Route::post('projects/{project}/registries', [RegistryController::class, 'store'])->name('registries.store');
    Route::get('projects/{project}/registries/{registry}/edit', [RegistryController::class, 'edit'])->name('registries.edit');
    Route::get('projects/{project}/registries/{registry}', [RegistryController::class, 'show'])->name('registries.show');
    Route::patch('projects/{project}/registries/{registry}', [RegistryController::class, 'update'])->name('registries.update');
    Route::delete('projects/{project}/registries/{registry}', [RegistryController::class, 'destroy'])->name('registries.destroy');

    Route::get('projects/{project}/workspaces/{workspace}/dashboard', [WorkspaceController::class, 'dashboard'])->name('workspaces.dashboard');
    Route::get('projects/{project}/workspaces/index', [WorkspaceController::class, 'index'])->name('workspaces.index');
    Route::get('/projects/{project}/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/projects/{project}/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::get('/projects/{project}/workspaces/{workspace}/edit', [WorkspaceController::class, 'edit'])->name('workspaces.edit');
    Route::patch('/projects/{project}/workspaces/{workspace}', [WorkspaceController::class, 'update'])->name('workspaces.update');
    Route::delete('/projects/{project}/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');

    //update registries new
    Route::get('/projects/{project}/workspaces/{workspace}/edit-registries', [WorkspaceController::class, 'editRegistries'])->name('workspaces.edit-registries');
    Route::patch('/projects/{project}/workspaces/{workspace}/sync-registries', [WorkspaceController::class, 'syncRegistries'])->name('workspaces.sync-registries');

    Route::get('/projects/{project}/workspaces/{workspace}/registries', [WorkspaceRegistryController::class, 'index'])->name('workspace.registries.index');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/{registry}', [WorkspaceRegistryController::class, 'show'])->name('workspace.registries.show');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/reports/create', [WorkspaceRegistryReportController::class, 'create'])->name('workspace.registry.reports.create');
    Route::get('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}', [WorkspaceRegistryReportController::class, 'edit'])->name('workspace.registry.reports.edit');
    Route::patch('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}', [WorkspaceRegistryReportController::class, 'update'])->name('workspace.registry.reports.update');
    Route::delete('/projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}', [WorkspaceRegistryReportController::class, 'destroy'])->name('workspace.registry.reports.destroy');
    Route::post('/projects/{project}/workspaces/{workspace}/registries/reports', [WorkspaceRegistryReportController::class, 'store'])->name('workspace.registry.reports.store');

});

require __DIR__.'/auth.php';
