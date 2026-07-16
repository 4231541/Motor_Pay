<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Requests\AssignAgentRequest;
use App\Http\Requests\Api\V1\Requests\StorePurchaseRequest;
use App\Http\Requests\Api\V1\Requests\UpdatePurchaseRequestStatus;
use App\Http\Resources\Api\V1\PurchaseRequestLogResource;
use App\Http\Resources\Api\V1\PurchaseRequestResource;
use App\Models\PurchaseRequest;
use App\Services\PurchaseRequestService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PurchaseRequestController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PurchaseRequestService $requestService) {}

    /**
     * List paginated requests (filtered by user role).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $requests = $this->requestService->getRequests($request->user(), $perPage);

        return $this->success(PurchaseRequestResource::collection($requests)->response()->getData(true));
    }

    /**
     * Store a new purchase request (Customer action).
     */
    public function store(StorePurchaseRequest $request): JsonResponse
    {
        Gate::authorize('create', PurchaseRequest::class);

        $purchaseRequest = $this->requestService->createRequest($request->user(), $request->validated());

        return $this->success(new PurchaseRequestResource($purchaseRequest), 'Request submitted successfully.', 201);
    }

    /**
     * Show a specific request.
     */
    public function show(PurchaseRequest $purchaseRequest): JsonResponse
    {
        Gate::authorize('view', $purchaseRequest);

        $purchaseRequest->load(['car.brand', 'car.carModel', 'car.media', 'user', 'assignedAgent']);

        return $this->success(new PurchaseRequestResource($purchaseRequest));
    }

    /**
     * Update request status (Agent/Admin action).
     */
    public function updateStatus(UpdatePurchaseRequestStatus $request, PurchaseRequest $purchaseRequest): JsonResponse
    {
        Gate::authorize('updateStatus', $purchaseRequest);

        $newStatus = \App\Enums\RequestStatus::from($request->input('status'));
        $notes = $request->input('notes');

        $updatedRequest = $this->requestService->updateStatus($purchaseRequest, $newStatus, $request->user(), $notes);

        return $this->success(new PurchaseRequestResource($updatedRequest->load(['car', 'user', 'assignedAgent'])), 'Status updated successfully.');
    }

    /**
     * Assign an agent to the request (Admin action).
     */
    public function assign(AssignAgentRequest $request, PurchaseRequest $purchaseRequest): JsonResponse
    {
        Gate::authorize('assign', $purchaseRequest);

        $updatedRequest = $this->requestService->assignAgent($purchaseRequest, $request->input('agent_id'));

        return $this->success(new PurchaseRequestResource($updatedRequest->load('assignedAgent')), 'Agent assigned successfully.');
    }

    /**
     * Get status history logs.
     */
    public function logs(PurchaseRequest $purchaseRequest): JsonResponse
    {
        Gate::authorize('view', $purchaseRequest);

        $logs = $purchaseRequest->statusLogs()->with('changedBy')->latest()->get();

        return $this->success(PurchaseRequestLogResource::collection($logs));
    }
}
