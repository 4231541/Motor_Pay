<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditService;

class UserObserver
{
    public function __construct(private readonly AuditService $auditService) {}

    public function created(User $user): void
    {
        $this->auditService->logModelEvent($user, 'created');
    }

    public function updated(User $user): void
    {
        $this->auditService->logModelEvent($user, 'updated');
    }

    public function deleted(User $user): void
    {
        $this->auditService->logModelEvent($user, 'deleted');
    }
}
