<?php

namespace App\Jobs\Search;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MeiliSearch\Client as MeiliSearch;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchMissingPostsImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private string $locale;

    /**
     * @var int
     */
    private int $postId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($locale, $postId)
    {
        $this->locale = $locale;
        $this->postId = $postId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // app locale
        $locale = $this->locale;
        $postId=$this->postId;

        //Update datetime
        $oldPost=DB::table('posts')->where('id',$postId)->first();
        DB::table('posts')->where('id',$postId)->update(["datetime" => $oldPost->updated_at]);
        
        // get meilisearch index
        $meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));
        $index = $meilisearch->index('posts_' . $locale);

        // get post
        $posts = Post::with([
            'translations',
            'category',
            'category.translations',
            'reporter',
            'reporter.translations'
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->where('id', '=', $postId)
            ->get();

        $objects = $posts->map(function ($model) {
            // remove all image tags
            $content = preg_replace("/<img[^>]+>/", "", $model->content);
            $content = strip_tags($content);

            $excerpt=$model->excerpt;
            // get excerpt content from content
            if(!isset($excerpt) && $model->content){
                $newStr= htmlspecialchars_decode($model->content);
                $excerptStr = Str::words(strip_tags($newStr));
                $excerpt= Str::substr($excerptStr, 0, 299);
            }
            return [
                'id' => $model->id,
                'title' => $model->title?$model->title:$model->short_title,
                'shortTitle' => $model->short_title,
                'excerpt' => $excerpt,
                'category' => $model->category?->name,
                'categorySlug' => '/' . $model->category?->slug,
                'reporter' => $model->reporter?->name,
                'content' =>  $content,
                'publishedAt' => $model->updated_at?->unix(),
                'categoryId' => $model->category?->id,
                'reporterId' => $model->reporter?->id,
                'slug' => '/' . $model->category?->slug . '/' . $model->slug,
                'image' => $model->image,
            ];
        })->filter()->values()->all();

        if (!empty($objects)) {
            $index->addDocuments($objects);
        }
    }
}
