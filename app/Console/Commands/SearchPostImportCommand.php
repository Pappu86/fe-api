<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use MeiliSearch\Client as MeiliSearch;

class SearchPostImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:import {index : The Index name of the Model} {--c|chunk= : The number of records to import at a time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import documents into MeiliSearch Index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('index');
        $chunk = $this->option('chunk') ?? 100;
        $locale = 'en';

        $meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));

        $index = $meilisearch->index($name);
        // $startDate = '2023-01-01 00:00:00';
        // $endDate = '2023-12-31 23:59:00';

        Post::with([
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
            // ->whereBetween('datetime', [date($startDate), date($endDate)])
            ->whereDate('datetime', '<=', now())
            ->chunk($chunk, function($posts) use($index) {
                $objects = $posts->map(function ($model) {
                    // remove all image tags
                    $content = preg_replace("/<img[^>]+>/", "", $model->content);
                    $content = strip_tags($content);
                    $excerpt=$model->excerpt;
                    // get excerpt content from main content
                    if(!isset($excerpt) && $content){
                        $newStr= htmlspecialchars_decode($content);
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
                        'publishedAt' => $model->datetime?->unix(),
                        'categoryId' => $model->category?->id,
                        'reporterId' => $model->reporter?->id,
                        'slug' => '/' . $model->category?->slug . '/' . $model->slug,
                        'image' => $model->image,
                    ];
                })->filter()->values()->all();

                if (!empty($objects)) {
                    $index->addDocuments($objects);
                }
            });

        return 0;
    }
}
