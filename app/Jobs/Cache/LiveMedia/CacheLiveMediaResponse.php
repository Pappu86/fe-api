<?php

namespace App\Jobs\Cache\LiveMedia;

use App\Traits\LiveMediaResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

class CacheLiveMediaResponse implements ShouldQueue
{
    use LiveMediaResponse;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected string $locale;

    /**
     * Create a new job instance.
     *
     * @param $locale
     * @return void
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Cache::forever('media_' . $this->locale, $this->get($this->locale));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
