<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'source' => $this->source,
            'customer_message' => $this->customer_message,
            'internal_notes' => $this->when($this->shouldShowNotes($request), $this->notes),
            'car' => new CarListResource($this->whenLoaded('car')),
            'customer' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ]),
            'assigned_agent' => $this->whenLoaded('assignedAgent', fn () => [
                'id' => $this->assignedAgent->id,
                'name' => $this->assignedAgent->name,
            ]),
            'logs' => PurchaseRequestLogResource::collection($this->whenLoaded('logs')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Determine if internal notes should be shown.
     * Customers shouldn't see internal agent notes.
     */
    private function shouldShowNotes(Request $request): bool
    {
        $user = $request->user();
        if (!$user) return false;

        // Admin/Agent can see notes. Customer only if they are not the owner (which shouldn't happen, but just in case)
        return $user->hasPermission('request.viewAny') || $user->id === $this->assigned_agent_id;
    }
}
