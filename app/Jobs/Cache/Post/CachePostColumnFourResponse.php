<?php

namespace App\Jobs\Cache\Post;

use App\Traits\PostResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

class CachePostColumnFourResponse implements ShouldQueue
{
    use PostResponse;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected string $locale;

    /**
     * @var string
     */
    protected string $type;

    /**
     * Create a new job instance.
     *
     * @param $locale
     * @param $type
     */
    public function __construct($locale, $type)
    {
        $this->locale = $locale;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Cache::forever('post_' . $this->type . '_' . $this->locale, $this->getColumnFourPosts($this->locale, $this->type));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
