<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserEditResource extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'status' => $this->status,
            'email' => $this->email,
            'email_verified_at' => (bool)$this->email_verified_at,
            'role' => collect($this->getRoleNames())->first()
        ];
    }
}
