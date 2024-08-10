<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSingleResource extends JsonResource
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
            'email' => $this->email,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'role' => collect($this->getRoleNames())->first(),
            'password' => '',
            'password_confirmation' => '',
            'email_verified_at' => (bool)$this->email_verified_at,
        ];
    }
}
