<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

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
        // Permissions helpers pour la sidebar
        Blade::if('canManageProduits', function() {
            return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGestionnaire());
        });

        Blade::if('canManageEntreesStock', function() {
            return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGestionnaire());
        });

        Blade::if('canManageTransferts', function() {
            return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGestionnaire());
        });

        Blade::if('canManageVentes', function() {
            return auth()->check(); // Tous les rôles peuvent voir les ventes
        });

        Blade::if('canManageRapports', function() {
            return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isGestionnaire());
        });

        // Notifications réelles pour la cloche du topbar (layouts.app)
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $view->with('navNotifications', auth()->user()->notifications()->latest()->limit(5)->get());
                $view->with('navUnreadCount', auth()->user()->unreadNotifications()->count());
            } else {
                $view->with('navNotifications', collect());
                $view->with('navUnreadCount', 0);
            }
        });
    }
}
