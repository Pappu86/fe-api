<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LiveMediaEditResource extends JsonResource
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
            'featured' => $this->featured,
            'title' => $this->title,
            'content' => $this->content,
            'subtitle' => $this->subtitle,
            'image' => $this->image,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
        ];
    }
}
