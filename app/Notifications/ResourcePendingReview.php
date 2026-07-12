<?php

namespace App\Notifications;

use App\Models\Resource;
use Illuminate\Notifications\Notification;

class ResourcePendingReview extends Notification
{
    public function __construct(protected Resource $resource)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'resource_id' => $this->resource->id,
            'resource_title' => $this->resource->title,
            'submitted_by' => $this->resource->user->name,
        ];
    }
}
