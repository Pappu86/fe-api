<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Slider;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CreateSliderFromPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $post, $locale;

    /**
     * Create a new job instance.
     *
     * @param $post
     * @return void
     */
    public function __construct($locale,$post)
    {
        $this->post = $post;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        App::setLocale($this->locale);
        $locale = $this->locale;

        // begin database transaction
        DB::beginTransaction();
        try {
            $category = Category::with('translations')
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->first();

            $maxOrdaring=Slider::max('ordering');
            $ordaring=1;
            
            if($maxOrdaring){
                $ordaring=($maxOrdaring+1)*1;
            }
            
            $slider = new Slider();
            $v = json_encode($this->post->image);

            $slider->fill([
                'title' => $this->post->short_title,
                'status' => true,
                'link' => '/'. $category->slug . '/' . $this->post->slug,
                'content' => json_decode($v),
                'ordering' => $ordaring
            ]);
            $slider->save();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'sliderId' => $slider->id
            ], 201);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }
}
