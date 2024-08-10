<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class UserLogResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'date' => $this->date,
            'login_status' => $this->login_status,
            'last_login' => $this->last_login,
            'last_logout' => $this->last_logout,
            'browser' => $this->browser,
            'os' => $this->os,
        ];
    }
}
