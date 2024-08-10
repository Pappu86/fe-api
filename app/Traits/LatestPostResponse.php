<?php

namespace App\Traits;

use App\Http\Resources\Api\LatestPostApiResource;
use App\Models\LatestPost;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;

trait LatestPostResponse
{
    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    protected function get($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $items = $this->getItems($locale)->get();
        ResourceCollection::withoutWrapping();

        return LatestPostApiResource::collection($items);
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getItems($locale): Builder
    {
        return LatestPost::with('translations')
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->latest();
    }
}
