<?php

namespace App\Http\Resources\Subscribers;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'status' => $this->status,
            'mailchimp_status' => $this->mailchimp_status,
            'created_at' => $this->created_at,
        ];
    }
}
