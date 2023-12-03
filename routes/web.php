<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectRegistryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceRegistryController;
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

Route::middleware('auth', 'verified', 'web')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/projects/{project}/dashboard', [ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    Route::get('projects/{project}/users', [UserController::class, 'index'])->name('users.index');
    Route::get('projects/{project}/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('projects/{project}/users', [UserController::class, 'store'])->name('users.store');
    Route::get('projects/{project}/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('projects/{project}/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('projects/{project}/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('projects/{project}/registries', [ProjectRegistryController::class, 'index'])->name('project.registries.index');
    Route::get('projects/{project}/create', [ProjectRegistryController::class, 'create'])->name('project.registries.create');
    Route::post('projects/{project}/registries', [ProjectRegistryController::class, 'store'])->name('project.registries.store');
    Route::get('projects/{project}/registries/{registry}', [ProjectRegistryController::class, 'edit'])->name('project.registries.edit');

    Route::get('projects/{project}/workspaces/{workspace}/dashboard', [WorkspaceController::class, 'dashboard'])->name('workspaces.dashboard');
    Route::get('/projects/{project}/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/projects/{project}/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::get('/projects/{project}/workspaces/{workspace}/edit', [WorkspaceController::class, 'edit'])->name('workspaces.edit');
    Route::patch('/projects/{project}/workspaces/{workspace}', [WorkspaceController::class, 'update'])->name('workspaces.update');
    Route::delete('/projects/{project}/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');

    Route::get('projects/{project}/workspaces/{workspace}/registries', [WorkspaceRegistryController::class, 'index'])->name('workspace.registries.index');

});

require __DIR__.'/auth.php';
