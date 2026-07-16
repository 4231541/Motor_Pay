<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\PurchaseRequest;
use App\Models\RequestStatusLog;
use App\Models\User;
use App\Notifications\PurchaseRequestStatusUpdated;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseRequestService
{
    /**
     * Get paginated requests based on user role.
     */
    public function getRequests(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = PurchaseRequest::with(['car.brand', 'car.carModel', 'car.media', 'user', 'assignedAgent']);

        if ($user->hasPermission('request.viewAny')) {
            // Admin sees all
        } elseif ($user->hasRole(\App\Enums\RoleName::SalesAgent->value)) {
            // Agent sees only assigned
            $query->where('assigned_agent_id', $user->id);
        } else {
            // Customer sees only theirs
            $query->where('user_id', $user->id);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new purchase request.
     */
    public function createRequest(User $customer, array $data): PurchaseRequest
    {
        $data['user_id'] = $customer->id;
        $data['status'] = RequestStatus::Received->value;
        $data['source'] = 'web'; // or from mobile if passed

        return PurchaseRequest::create($data);
    }

    /**
     * Update request status within a transaction and log it.
     */
    public function updateStatus(PurchaseRequest $request, RequestStatus $newStatus, User $user, ?string $notes = null): PurchaseRequest
    {
        if ($request->status === $newStatus) {
            return $request;
        }

        DB::beginTransaction();

        try {
            $oldStatus = $request->status;

            // Log the change
            RequestStatusLog::create([
                'request_id' => $request->id,
                'changed_by' => $user->id,
                'old_status' => $oldStatus->value,
                'new_status' => $newStatus->value,
                'notes' => $notes,
            ]);

            // Update status
            $request->update(['status' => $newStatus->value]);

            DB::commit();

            // Notify Customer
            $request->user->notify(new PurchaseRequestStatusUpdated($request, $newStatus));

            return $request->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign an agent to the request.
     */
    public function assignAgent(PurchaseRequest $request, int $agentId): PurchaseRequest
    {
        $request->update(['assigned_agent_id' => $agentId]);
        return $request->refresh();
    }
}
