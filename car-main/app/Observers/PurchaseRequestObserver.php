<?php

namespace App\Observers;

use App\Models\PurchaseRequest;
use App\Services\AuditService;

class PurchaseRequestObserver
{
    public function __construct(private readonly AuditService $auditService) {}

    public function created(PurchaseRequest $request): void
    {
        $this->auditService->logModelEvent($request, 'created');
    }

    public function updated(PurchaseRequest $request): void
    {
        $this->auditService->logModelEvent($request, 'updated');
    }

    public function deleted(PurchaseRequest $request): void
    {
        $this->auditService->logModelEvent($request, 'deleted');
    }
}
