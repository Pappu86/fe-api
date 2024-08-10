<?php

namespace App\Http\Resources\Api;

use App\Traits\PostSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CategoryMorePostApiResource extends JsonResource
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
        if($this->content){
            $newStr= htmlspecialchars_decode($this->content);
            $excerptStr = Str::words(strip_tags($newStr));
            $excerpt= Str::substr($excerptStr, 0, 299);
        }
        return [
            'id' => $this->id,
            'title' => $this->short_title ?? $this->title,
            'slug' => ($this->category && $this->slug)? $this->getSlug($this->category, $this->slug):'',
            'image' => $this->image,
            'caption' => $this->caption ?? $this->short_title ?? $this->title,
            'excerpt' => $excerpt,
            'datetime' => $this->datetime,
            'category' => CategoryCommonApiResource::make($this->category)
        ];
    }
}
