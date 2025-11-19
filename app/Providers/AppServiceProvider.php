<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Models\Vehicle;
use App\Policies\TicketPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Support\Facades\Gate;
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
        // Registrar políticas
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(Vehicle::class, VehiclePolicy::class);
    }
}

