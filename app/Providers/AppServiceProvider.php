<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use App\Http\Livewire\BreweryList;
use App\Http\Livewire\Navbar;

class AppServiceProvider extends ServiceProvider
{
    private static $bootExecuted = false;
    private static $registerExecuted = false;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // LOG AVVIO
        if (!self::$registerExecuted) {
            Log::info("AppServiceProvider: Inizializzazione della registrazione dei servizi");
            self::$registerExecuted = true;
        }

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // LOG AVVIO
        if (!self::$bootExecuted) {
            Log::info("AppServiceProvider: Avvio bootstrap applicazione");
            self::$bootExecuted = true;
        }

        try {
            // LIVEWIRE
            Livewire::component('brewery-list', BreweryList::class);
            Livewire::component('navbar', Navbar::class);

            // LOG AVVIO
            if (self::$bootExecuted) {
                Log::info("AppServiceProvider: Componenti Livewire registrati con successo");
                Log::info("AppServiceProvider: Bootstrap completato");
            }
        } catch (\Exception $e) {
            // LOG ERRORI
            Log::error("AppServiceProvider: Errore nella registrazione dei componenti Livewire", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
