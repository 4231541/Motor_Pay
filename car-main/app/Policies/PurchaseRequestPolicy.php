<?php

namespace App\Policies;

use App\Models\PurchaseRequest;
use App\Models\User;

class PurchaseRequestPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a request
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseRequest $purchaseRequest): bool
    {
        if ($user->hasPermission('request.viewAny')) {
            return true;
        }

        if ($user->hasRole(\App\Enums\RoleName::SalesAgent) && (int) $purchaseRequest->assigned_agent_id === (int) $user->id) {
            return true;
        }

        return (int) $user->id === (int) $purchaseRequest->user_id;
    }

    /**
     * Determine whether the user can update the status of the model.
     */
    public function updateStatus(User $user, PurchaseRequest $purchaseRequest): bool
    {
        if ($user->hasPermission('request.updateStatus')) {
            return true;
        }

        // Agents can update if assigned
        if ($user->hasRole(\App\Enums\RoleName::SalesAgent) && (int) $purchaseRequest->assigned_agent_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can assign an agent.
     */
    public function assign(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->hasPermission('request.assign');
    }
}
