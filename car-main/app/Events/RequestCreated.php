<?php

namespace App\Events;

use App\Models\PurchaseRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param PurchaseRequest $request The newly created request model.
     */
    public function __construct(
        public readonly PurchaseRequest $request
    ) {}
}
