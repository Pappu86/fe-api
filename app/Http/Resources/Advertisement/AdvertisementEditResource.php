<?php

namespace App\Http\Resources\Advertisement;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'provider_id' => $this->provider_id,
            'position_id' => $this->position_id,
            'status' => $this->status,
            'type' => $this->type,
            'title' => $this->title,
            'is_modal' => $this->is_modal,
            'is_auto_modal' => $this->is_auto_modal,
            'is_external' => $this->is_external,
            'content' => $this->content,
            'link' => $this->link,
            'has_mobile_content' => $this->has_mobile_content,
            'mobile_content' => $this->mobile_content,
            'mobile_link' => $this->mobile_link,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'clicks_count' => $this->clicks_count,
            'auto_modal_duration' => $this->auto_modal_duration,
            'position' => $this->position,
            'total_additional_ads' => $this->getMultipleAds()->count(),
            'additional_ads' => $this->getMultipleAds()->get(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

       // return parent::toArray($request);
    }
}