<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use App\Services\ActivityNotificationService;
use Illuminate\Database\Eloquent\Model;

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

        $this->registerActivityNotifications();
    }

    /**
     * Notifie automatiquement la hiérarchie (gestionnaire + admins, ou
     * admins seuls) lorsqu'un vendeur ou un gestionnaire crée/modifie/
     * supprime une ressource métier suivie.
     */
    private function registerActivityNotifications(): void
    {
        Event::listen(
            ['eloquent.created: *', 'eloquent.updated: *', 'eloquent.deleted: *'],
            function (string $eventName, array $data): void {
                $model = $data[0] ?? null;
                if (!$model instanceof Model) {
                    return;
                }

                $service = app(ActivityNotificationService::class);
                if (!$service->estSuivi($model)) {
                    return;
                }

                $service->notifier($model, $service->verbeDepuisEvenement($eventName));
            }
        );
    }
}
