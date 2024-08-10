<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'positionId' => $this->position_id,
            'type' => $this->type,
            'content' => $this->content,
            'link' => $this->link,
            'hasMobileContent' => $this->has_mobile_content,
            'mobileContent' => $this->mobile_content,
            'mobileLink' => $this->mobile_link,
            'isExternal' => $this->is_external,
            'isModal' => $this->is_modal,
            'isAutoModal' => $this->is_auto_modal,
            'autoModalDuration' => $this->auto_modal_duration,
            'totalAdditionalAds' => $this->getMultipleAds()->count(),
            'additionalAds' => $this->getMultipleAds()->get(),
        ];
    }
}