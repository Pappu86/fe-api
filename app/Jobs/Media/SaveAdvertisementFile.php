<?php

namespace App\Jobs\Media;

use App\Models\Advertisement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SaveAdvertisementFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Advertisement
     */
    protected Advertisement $advertisement;

    /**
     * @var string
     */
    protected string $key;

    /**
     * @var string
     */
    protected string $field;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Advertisement $advertisement, $key, $field)
    {
        $this->advertisement = $advertisement;
        $this->key = $key;
        $this->field = $field;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->advertisement->forceFill([
                $this->field => $this->advertisement->getFirstMediaUrl($this->key)
            ]);
            $this->advertisement->save();
        } catch (Throwable $e) {
            report($e);
        }
    }
}
