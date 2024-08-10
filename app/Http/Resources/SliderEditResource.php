<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SliderEditResource extends JsonResource
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
            'status' => $this->status,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'link' => $this->link,
            'ordering' => $this->ordering,
            'is_external' => $this->is_external
        ];
    }
}
