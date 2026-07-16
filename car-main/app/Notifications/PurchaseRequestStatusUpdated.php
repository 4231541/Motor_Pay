<?php

namespace App\Notifications;

use App\Enums\RequestStatus;
use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseRequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly PurchaseRequest $purchaseRequest,
        public readonly RequestStatus $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on your car request')
            ->line('The status of your request for the ' . $this->purchaseRequest->car->title . ' has been updated.')
            ->line('New Status: ' . $this->newStatus->label())
            ->action('View Request', url('/requests/' . $this->purchaseRequest->id))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->purchaseRequest->id,
            'car_title' => $this->purchaseRequest->car->title,
            'status' => $this->newStatus->value,
            'message' => 'Your request status was updated to ' . $this->newStatus->label(),
        ];
    }
}
