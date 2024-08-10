<?php

namespace App\Traits;

use App\Http\Resources\Api\SliderApiResource;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;

trait SliderResponse
{
    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    protected function getSliders($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $sliders = Slider::with('translations')
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->orderBy('ordering')
            ->get();
        ResourceCollection::withoutWrapping();

        return SliderApiResource::collection($sliders);
    }
}
