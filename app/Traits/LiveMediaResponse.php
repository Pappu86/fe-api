<?php

namespace App\Traits;

use App\Http\Resources\Api\LiveMediaApiResource;
use App\Models\LiveMedia;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;

trait LiveMediaResponse
{
    /**
     * @param $locale
     * @return LiveMediaApiResource|null
     */
    protected function getFeatured($locale): ?LiveMediaApiResource
    {
        App::setLocale($locale);

        $media = $this->getItems($locale)
            ->where('featured', '=', 1)
            ->first();
        ResourceCollection::withoutWrapping();

        if ($media) {
            return LiveMediaApiResource::make($media);
        } else {
            return null;
        }
    }

    /**
     * @param $locale
     * @param int $limit
     * @return AnonymousResourceCollection
     */
    protected function get($locale, int $limit = 4): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $items = $this->getItems($locale)->limit($limit)->get();
        ResourceCollection::withoutWrapping();

        return LiveMediaApiResource::collection($items);
    }

    /**
     * @param $locale
     * @param int $limit
     * @return CursorPaginator
     */
    protected function getWithPaginate($locale, int $limit = 8): CursorPaginator
    {
        App::setLocale($locale);

        return $this->getItems($locale)->orderByDesc('id')->cursorPaginate($limit);
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getItems($locale): Builder
    {
        return LiveMedia::with('translations')
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->orderByDesc('start_at');
    }
}
