<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

//     use Illuminate\Support\Facades\View;
// use App\Models\Notification;

// public function boot()
// {
//     View::composer('*', function ($view) {
//         if (auth()->check()) {
//             $notifications = Notification::where('user_id', auth()->id())
//                 ->latest()
//                 ->take(5)
//                 ->get();

//             $view->with('notifications', $notifications);
//         }
//     });
// }
}
