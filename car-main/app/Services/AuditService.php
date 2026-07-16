<?php

namespace App\Services;

use App\Contracts\AuditableInterface;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService implements AuditableInterface
{
    /**
     * Log an auditable action.
     */
    public function logAction(string $event, array $context = [], ?int $userId = null): void
    {
        AuditLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $event,
            'auditable_type' => $context['auditable_type'] ?? null,
            'auditable_id' => $context['auditable_id'] ?? null,
            'old_values' => $context['old_values'] ?? null,
            'new_values' => $context['new_values'] ?? null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log Eloquent model changes.
     */
    public function logModelEvent(Model $model, string $action): void
    {
        $oldValues = $action === 'updated' || $action === 'deleted' ? $model->getOriginal() : null;
        $newValues = $action === 'created' || $action === 'updated' ? $model->getAttributes() : null;

        // Strip hidden fields like passwords
        $hidden = $model->getHidden();
        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($hidden));
        }
        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($hidden));
        }

        $this->logAction($action, [
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
