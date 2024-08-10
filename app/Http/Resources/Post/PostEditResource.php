<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;

class PostEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'reporter_id' => $this->reporter_id,
            'reporter_name' => $this->reporter?->name,
            'type' => $this->type,
            'title' => $this->title,
            'short_title' => $this->short_title,
            'slug' => $this->slug,
            'shoulder' => $this->shoulder,
            'hanger' => $this->hanger,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'is_fb_article' => $this->is_fb_article,
            'image' => $this->image,
            'caption' => $this->caption,
            'source' => $this->source,
            'meta_image' => $this->meta_image,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'datetime' => $this->datetime,
        ];
    }
}