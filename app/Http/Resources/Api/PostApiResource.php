<?php

namespace App\Http\Resources\Api;

use App\Traits\PostSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PostApiResource extends JsonResource
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
        // Remove .cdn from image url
        $metaImage='';
        if($this->image){
            $expImages = explode(".cdn",$this->image);
            if(!empty($expImages)){
                $metaImage=$expImages[0].$expImages[1];
            }   
        } 

        return [
            'id' => $this->id,
            'title' => $this->title ? $this->title:$this->short_title,
            'slug' => ($this->category && $this->slug)? $this->getSlug($this->category, $this->slug):'',
            'image' => $this->image,
            'caption' => $this->caption,
            'source' => $this->source,
            'excerpt' => $excerpt,
            'publishedAt' => $this->datetime,
            'updatedAt' => $this->updated_at,
            'category' => CategoryCommonApiResource::make($this->category),
            'content' => $this->content,
            'reporter' => ReporterPostApiResource::make($this->reporter),
            'shoulder' => $this->shoulder,
            'hanger' => $this->hanger,
            'metaImage' => $metaImage,
        ];
    }
}