<?php

namespace App\Jobs\Cache\Category;

use App\Traits\CategoryResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Throwable;

class CacheCategoryMoreResponse implements ShouldQueue
{
    use CategoryResponse;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    protected string $locale;

    /**
     * Create a new job instance.
     *
     * @param $locale
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
            Cache::forever('post_category_more_' . $this->locale, $this->getCategoryMoreResponse($this->locale));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
