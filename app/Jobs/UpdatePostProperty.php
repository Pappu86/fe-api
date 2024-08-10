<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Models\PostTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UpdatePostProperty implements ShouldQueue
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
        $posts = PostTranslation::query()->whereNull('title')->whereIn('id',$this->postIds)->get();        
        foreach ($posts as $post) {
            $postId=$post->id;
            $title = $post->title;
            $reporterId = $post->reporter_id;
            // DB::table('post_translations')->where('post_id',$post->id)->update(["title" => $post->short_title]);
            // DB::table('posts')->where('id', $post->id)->update(['reporter_id'=> 3]);
            // DB::table('posts')->where('id', $post->id)->update(['datetime'=> $post->updated_at]);            
             if(!isset($title)&& isset($post->short_title)){              
                DB::table('post_translations')->where('id',$post->id)->update(["title" => $post->short_title]);
             }

            // if(!isset($reporterId)){
            //    // Log::info("isset-Null-reporterId", [$postId]);
            //     DB::table('posts')->where('id', $post->id)->update(['reporter_id'=> 3]);
            // }
        }

    }
}