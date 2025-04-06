<?php

namespace App\Policies;

use App\Models\UserScanProgress;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserScanProgressPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserScanProgress $userScanProgress): bool
    {
        return $user->id === $userScanProgress->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Allow authenticated users to create scans
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserScanProgress $userScanProgress): bool
    {
        // Check if the user has a UserScanProgress record for this scan
        return $user->id === $userScanProgress->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserScanProgress $userScanProgress): bool
    {
        // Check if the user has a UserScanProgress record for this scan
        return $user->id === $userScanProgress->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserScanProgress $userScanProgress): bool
    {
        return false; // Soft deletes not implemented
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserScanProgress $userScanProgress): bool
    {
        return false; // Force deletes not implemented
    }
}
