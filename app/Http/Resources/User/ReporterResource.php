<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class ReporterResource extends JsonResource
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
            'status' => $this->status,
            'username' => $this->username,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'social_links' => $this->social_links,
        ];
    }
}
