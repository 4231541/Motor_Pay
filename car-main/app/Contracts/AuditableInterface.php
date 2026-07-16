<?php

namespace App\Contracts;

/**
 * AuditableInterface
 *
 * All services that need to record audit trails must inject this interface.
 * The concrete implementation (AuditService) will be completed in Phase 3.
 * Using this contract now ensures zero refactoring is needed later.
 */
interface AuditableInterface
{
    /**
     * Log an auditable action.
     *
     * @param  string               $event      A descriptive event name, e.g. "car.created"
     * @param  array<string, mixed> $context    Contextual data for the log (model type, id, changes, etc.)
     * @param  int|null             $userId     The acting user's ID. Null for system-driven events.
     * @return void
     */
    public function logAction(string $event, array $context = [], ?int $userId = null): void;
}
