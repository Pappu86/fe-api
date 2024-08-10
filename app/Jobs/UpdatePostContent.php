<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\Search\DeletePostDocument;
use Illuminate\Support\Facades\Log;

class UpdatePostContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $postIds;

    /**
     * Create a new job instance.
     *
     * @param $postIds
     */
    public function __construct($postIds)
    {
        $this->postIds = $postIds;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        $posts = $this->postIds;        
        foreach ($posts as $post) {
            // Log::info("post",[$post]);
            DeletePostDocument::dispatch('en', $post);          
        }

    }
}