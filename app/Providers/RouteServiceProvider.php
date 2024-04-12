<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @return string
     */
    public static function home()
    {

        if (auth()->check() && auth()->user()->project_id) {

            return route('projects.dashboard', ['project' => auth()->user()->project_id]);
        } elseif (auth()->check() && auth()->user()->project_id === null) {

            return route('projects.dashboard');
        }

        return '/';
    }

    //    /**
    //     * Define your route model bindings, pattern filters, and other route configuration.
    //     */
    //    public function boot(): void
    //    {
    //        RateLimiter::for('api', function (Request $request) {
    //            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    //        });
    //
    //        $this->routes(function () {
    //            Route::middleware('api')
    //                ->prefix('api')
    //                ->group(base_path('routes/api.php'));
    //
    //            Route::middleware('web')
    //                ->group(base_path('routes/web.php'));
    //        });
    //    }
}
