<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveMediaApiResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'subtitle' => $this->subtitle,
            'image' => $this->image,
            'startAt' => $this->start_at,
            'endAt' => $this->end_at,
        ];
    }
}
