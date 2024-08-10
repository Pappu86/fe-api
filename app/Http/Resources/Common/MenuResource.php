<?php

namespace App\Http\Resources\Common;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class MenuResource extends JsonResource
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
            'title' => $this->title,
            'icon' => $this->icon,
            'link' => $this->link,
            'status' => $this->status,
            'roles' => collect($this->roles)->pluck('id'),
            'children' => MenuChildrenResource::collection($this->whenLoaded('children')),
        ];
    }
}
