<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'office_id' => $this->office_id,
            'document_id' => $this->document_id,
            'message' => $this->message,
            'status' => $this->status,
            'badge' => $this->badge,
            'tracking_code' => $this->tracking_code,
            'subject' => $this->subject,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
