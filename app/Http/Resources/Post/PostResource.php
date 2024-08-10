<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $postSlug='';
        if($this->status){
            $postSlug= $this->category?->slug . '/' . $this->slug;
        } 

        return [
            'id' => $this->id,
            'datetime' => $this->datetime,
            'title' => $this->title,
            'slug' => $postSlug,
            'author' => $this->user?->name,
            'is_edited' => $this->is_edited,
            'category' => $this->category?->name,
            'status' => $this->status,
            'type' => $this->type,
            'image' => $this->image,
            'user' => $this->user,  
            'history' => $this->history          
        ];
    }
}