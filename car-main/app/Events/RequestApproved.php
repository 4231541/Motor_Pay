<?php

namespace App\Events;

use App\Models\PurchaseRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param PurchaseRequest $request The approved request.
     */
    public function __construct(
        public readonly PurchaseRequest $request
    ) {}
}
