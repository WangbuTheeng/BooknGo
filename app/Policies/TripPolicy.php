<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Trip $trip): bool
    {
        return $user->isAdmin() || 
               ($user->isOperator() && $user->operator->id === $trip->bus->operator_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Trip $trip): bool
    {
        return $user->isAdmin() || 
               ($user->isOperator() && $user->operator->id === $trip->bus->operator_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Trip $trip): bool
    {
        return $user->isAdmin() || 
               ($user->isOperator() && $user->operator->id === $trip->bus->operator_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Trip $trip): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Trip $trip): bool
    {
        return $user->isAdmin();
    }
}
