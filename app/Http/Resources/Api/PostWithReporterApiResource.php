<?php

namespace App\Http\Resources\Api;

use App\Traits\PostSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostWithReporterApiResource extends JsonResource
{
    use PostSlug;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->short_title ?? $this->title,
            'slug' => ($this->category && $this->slug)?$this->getSlug($this->category, $this->slug):'',
            'image' => $this->image,
            'caption' => $this->caption ?? $this->short_title ?? $this->title,
            'datetime' => $this->datetime,
            'reporter' => ReporterPostApiResource::make($this->reporter)
        ];
    }
}
