<?php

namespace App\Policies;

use App\Models\AcceptProductOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AcceptProductOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcceptProductOrder $acceptProductOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcceptProductOrder $acceptProductOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcceptProductOrder $acceptProductOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AcceptProductOrder $acceptProductOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AcceptProductOrder $acceptProductOrder): bool
    {
        return false;
    }
}
