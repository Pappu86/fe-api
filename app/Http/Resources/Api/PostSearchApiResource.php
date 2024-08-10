<?php

namespace App\Http\Resources\Api;

use App\Traits\PostSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PostSearchApiResource extends JsonResource
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
        $excerpt=$this->excerpt;
        if(!isset($excerpt) && $this->content){
            $newStr= htmlspecialchars_decode($this->content);
            $excerptStr = Str::words(strip_tags($newStr));
            $excerpt= Str::substr($excerptStr, 0, 299);
        }
        return [
            'id' => $this->id,
            'title' => $this->short_title ?? $this->title,
            'slug' => '/'.$this->category_slug.'/'.$this->slug,
            'category_id' =>$this->category_id,
            'category' =>$this->category_name,
            'reporter_id'=> $this->reporter_id,
            'reporter'=> $this->reporter_name,
            'reporter_username'=> $this->reporter_username,
            'image' => $this->image,
            'excerpt' => $excerpt,
            'datetime' => $this->datetime,
            'publishedAt'=>$this->datetime ?? $this->created_at,
        ];
    }
}
