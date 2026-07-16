<?php

namespace App\Events;

use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param PurchaseRequest $request The assigned request.
     * @param User            $agent   The agent being assigned.
     */
    public function __construct(
        public readonly PurchaseRequest $request,
        public readonly User $agent
    ) {}
}
