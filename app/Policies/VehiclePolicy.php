<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    /**
     * Determine if the user can view any vehicles.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine if the user can view the vehicle.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return $user->is_active;
    }

    /**
     * Determine if the user can create vehicles.
     */
    public function create(User $user): bool
    {
        return $user->isEncargado();
    }

    /**
     * Determine if the user can update the vehicle.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->isEncargado();
    }

    /**
     * Determine if the user can delete the vehicle.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        // No se puede eliminar si tiene tickets asociados
        if ($vehicle->tickets()->count() > 0) {
            return false;
        }
        
        return $user->isEncargado();
    }
}
