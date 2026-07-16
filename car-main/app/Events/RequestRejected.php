<?php

namespace App\Events;

use App\Models\PurchaseRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param PurchaseRequest $request        The rejected request.
     * @param string|null     $reason         The rejection reason / note.
     */
    public function __construct(
        public readonly PurchaseRequest $request,
        public readonly ?string $reason = null
    ) {}
}
