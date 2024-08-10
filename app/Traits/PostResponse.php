<?php

namespace App\Traits;

use App\Http\Resources\Api\PostApiResource;
use App\Http\Resources\Api\PostColumnFourApiResource;
use App\Http\Resources\Api\PostColumnOneApiResource;
use App\Http\Resources\Api\PostColumnThreeApiResource;
use App\Http\Resources\Api\PostColumnTwoApiResource;
use App\Http\Resources\Api\PostWithImageApiResource;
use App\Http\Resources\Api\PostWithTextApiResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

trait PostResponse
{
    use CategoryResponse;

    /**
     * @param $locale
     * @param $slug
     * @return PostApiResource|null
     */
    protected function getPost($locale, $slug): ?PostApiResource
    {
        App::setLocale($locale);
        $post = Post::with([
            'translations',
            'category',
            'category.translations',
            'reporter',
            'reporter.translations'
        ])
            ->whereHas('translations', function (Builder $q) use ($locale, $slug) {
                $q->where('locale', '=', $locale)
                    ->where('slug', '=', $slug);
            })
            ->where('status', '=', 1)
            ->where('datetime', '<=', now())
            // ->whereDate('datetime', '<=', now())
            ->first();
        ResourceCollection::withoutWrapping();

        if ($post) {
            return PostApiResource::make($post);
        } else {
            return null;
        }
    }

    /**
     * @param $locale
     * @param $slug
     * @return AnonymousResourceCollection
     */
    protected function getPostMore($locale, $slug): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $post = Post::with('translations')
            ->whereHas('translations', function (Builder $q) use ($locale, $slug) {
                $q->where('locale', '=', $locale)
                    ->where('slug', '=', $slug);
            })
            ->where('status', '=', 1)
            ->where('datetime', '<=', now())
            // ->whereDate('datetime', '<=', now())
            ->first();

        // get latest posts
        $latestPosts = Post::with([
            'translations',
            'category',
            'category.translations',
            'reporter',
            'reporter.translations'
        ])
            ->whereHas('translations', function (Builder $q) use ($locale, $slug) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->where('datetime', '<=', now())
            // ->whereDate('datetime', '<=', now())
            ->where('id', '!=', $post?->id)
            ->where('category_id', '=', $post?->category->id)
            ->orderByDesc('datetime')
            ->limit(4)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostApiResource::collection($latestPosts);
    }

    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    protected function getPostMostRead($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        // get most read posts
        $posts = $this->getTodayPostsByViewCount($locale)
            ->limit(4)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostWithImageApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @param $type
     * @return AnonymousResourceCollection
     */
    protected function getColumnOnePosts($locale, $type): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $posts = $this->getColumnPosts($locale, $type)
            ->limit(5)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostColumnOneApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @param $type
     * @return AnonymousResourceCollection
     */
    protected function getColumnTwoPosts($locale, $type): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $posts = $this->getColumnPosts($locale, $type)
            ->limit(3)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostColumnTwoApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @param $type
     * @return AnonymousResourceCollection
     */
    protected function getColumnThreePosts($locale, $type): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $posts = $this->getColumnPosts($locale, $type)
            ->limit(4)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostColumnThreeApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @param $type
     * @return AnonymousResourceCollection
     */
    protected function getColumnFourPosts($locale, $type): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $posts = $this->getColumnPosts($locale, $type)
            ->limit(4)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostColumnFourApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @param $type
     * @return Builder
     */
    private function getColumnPosts($locale, $type): Builder
    {
        return Post::with([
            'translations',
            'category',
            'category.translations'
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->where('datetime', '<=', now()) 
            // ->whereDate('datetime', '<=', now())
            ->where('type', '=', $type)
            ->orderByDesc('datetime');
    }
}