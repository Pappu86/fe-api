<?php

namespace App\Jobs\Search;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MeiliSearch\Client as MeiliSearch;

class DeletePostDocument implements ShouldQueue
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
        // get meilisearch index
        $meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));
        $index = $meilisearch->index('posts_' . $locale);
        Log::info("index_name", [$index]);

        $index->deleteDocument($this->postId);
    }
}
