<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSearchResource extends JsonResource
{
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
            'title' => $this->title,
            'shortTitle' => $this->short_title,
            'excerpt' => $this->excerpt,
            'category' => $this->category?->name,
            'reporter' => $this->reporter?->name,
            'content' => $this->content,
            'publishedAt' => $this->datetime,
        ];
    }
}
