<?php

namespace App\Observers;

use App\Models\Car;
use App\Services\AuditService;

class CarObserver
{
    public function __construct(private readonly AuditService $auditService) {}

    public function created(Car $car): void
    {
        $this->auditService->logModelEvent($car, 'created');
    }

    public function updated(Car $car): void
    {
        $this->auditService->logModelEvent($car, 'updated');
    }

    public function deleted(Car $car): void
    {
        $this->auditService->logModelEvent($car, 'deleted');
    }
}
