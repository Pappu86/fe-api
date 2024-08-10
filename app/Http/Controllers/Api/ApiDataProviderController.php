<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Slider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Traits\SliderResponse;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Category;
use Carbon\Carbon;

class ApiDataProviderController extends Controller
{
    use SliderResponse;
     //
    public $url = 'http://thefinancialexpress.com.bd';

    public function getHome(Request $request)
    {
        // Log::info("Pappu testing");
        $locale="en";
        $slider = Slider::with('translations')
                ->whereHas('translations', function (Builder $q) use ($locale) {
                    $q->where('locale', '=', $locale);
                })
                ->where('status', '=', 1)
                ->orderBy('ordering')
                ->get()
                ->map(function ($slider) {
                    return [
                    'id'=>$slider->id,
                    'title'=>Str::words($slider->title,7),
                    'short_title'=>Str::words($slider->title,7),
                    'image_raw'=>$slider?->content,
                    'permalink'=>$this->url.'/'.$slider?->link,
                    'created_at'=>$slider->created_at,           
                    ];
                });

        // Economy
        $economyCategoryId = (int)config('config.category.economy');
        $economyCategory = Category::with('translations')
            ->where('id', '=', $economyCategoryId)
            ->first();

        if ($economyCategory) {
        $featured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $economyCategoryId)
            ->first();

        $economy = $this->getPosts($locale)
            ->where('category_id', '=', $economyCategoryId)
            ->where('id', '!=', $featured?->id)
            ->limit(2)
            ->get()
            ->map(function ($economy) {
                return [
                'id'=>$economy->id,
                'title'=>Str::words($economy->title,7),
                'short_title'=>Str::words($economy->short_title,7),
                'image_raw'=>$economy->image,
                'permalink'=>$this->url.'/'.$economy->cat_slug.'/'.$economy->slug,
                'created_at'=>$economy->created_at         
                ];
            });

            $economy_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $economyCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($economy_f) {
            return [
            'id'=>$economy_f->id,
            'title'=>Str::words($economy_f->title,7),
            'short_title'=>Str::words($economy_f->short_title,7),
            'image'=>$economy_f->image,
            'image_raw'=>$economy_f->image,
            'permalink'=>$this->url.'/'.$economy_f->cat_slug.'/'.$economy_f->slug,
            'created_at'=>$economy_f->created_at    
            ];
            });
        }

        // Stock
        $stockCategoryId = (int)config('config.category.stock');
        $stockCategory = Category::with('translations')
            ->where('id', '=', $stockCategoryId)
            ->first(); 

        if ($stockCategory) {
            $stockFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $stockCategoryId)
            ->first();

            $stock = $this->getPosts($locale)
                ->where('category_id', '=', $stockCategoryId)
                ->where('id', '!=', $stockFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($stock) {
                    return [
                    'id'=>$stock->id,
                    'title'=>Str::words($stock->title,7),
                    'short_title'=>Str::words($stock->short_title,7),
                    'image_raw'=>$stock->image,
                    'permalink'=>$this->url.'/'.$stock->cat_slug.'/'.$stock->slug,
                    'is_slider'=>'',
                    'created_at'=>$stock->created_at         
                    ];
                });

            $stock_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $stockCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($stock_f) {
                return [
                'id'=>$stock_f->id,
                'title'=>Str::words($stock_f->title,7),
                'short_title'=>Str::words($stock_f->short_title,7),
                'image'=>$stock_f->image,
                'image_raw'=>$stock_f->image,
                'permalink'=>$this->url.'/'.$stock_f->cat_slug.'/'.$stock_f->slug,
                'created_at'=>$stock_f->created_at    
                ];
            });
        }

        // Trade
        $tradeCategoryId = (int)config('config.category.trade');
        $tradeCategory = Category::with('translations')
            ->where('id', '=', $tradeCategoryId)
            ->first(); 

        if ($tradeCategory) {
            $tradeFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $tradeCategoryId)
            ->first();

            $trade = $this->getPosts($locale)
                ->where('category_id', '=', $tradeCategoryId)
                ->where('id', '!=', $tradeFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($trade) {
                    return [
                    'id'=>$trade->id,
                    'title'=>Str::words($trade->title,7),
                    'short_title'=>Str::words($trade->short_title,7),
                    'image_raw'=>$trade->image,
                    'permalink'=>$this->url.'/'.$trade->cat_slug.'/'.$trade->slug,
                    'created_at'=>$trade->created_at         
                    ];
                });

            $trade_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $tradeCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($trade_f) {
                return [
                'id'=>$trade_f->id,
                'title'=>Str::words($trade_f->title,7),
                'short_title'=>Str::words($trade_f->short_title,7),
                'image'=>$trade_f->image,
                'image_raw'=>$trade_f->image,
                'permalink'=>$this->url.'/'.$trade_f->cat_slug.'/'.$trade_f->slug,
                'created_at'=>$trade_f->created_at    
                ];
            });
        }

        // National
        $nationalCategoryId = (int)config('config.category.national');
        $nationalCategory = Category::with('translations')
            ->where('id', '=', $nationalCategoryId)
            ->first(); 

        if ($nationalCategory) {
            $nationalFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $nationalCategoryId)
            ->first();

            $national = $this->getPosts($locale)
                ->where('category_id', '=', $nationalCategoryId)
                ->where('id', '!=', $nationalFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($national) {
                    return [
                    'id'=>$national->id,
                    'title'=>Str::words($national->title,7),
                    'short_title'=>Str::words($national->short_title,7),
                    'image_raw'=>$national->image,
                    'permalink'=>$this->url.'/'.$national->cat_slug.'/'.$national->slug,
                    'created_at'=>$national->created_at         
                    ];
                });

            $national_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $nationalCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($national_f) {
                return [
                'id'=>$national_f->id,
                'title'=>Str::words($national_f->title,7),
                'short_title'=>Str::words($national_f->short_title,7),
                'image'=>$national_f->image,
                'image_raw'=>$national_f->image,
                'permalink'=>$this->url.'/'.$national_f->cat_slug.'/'.$national_f->slug,
                'created_at'=>$national_f->created_at    
                ];
            });
        }

        // World
        $worldCategoryId = (int)config('config.category.world');
        $worldCategory = Category::with('translations')
            ->where('id', '=', $worldCategoryId)
            ->first(); 

        if ($worldCategory) {
            $worldFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $worldCategoryId)
            ->first();

            $world = $this->getPosts($locale)
                ->where('category_id', '=', $worldCategoryId)
                ->where('id', '!=', $worldFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($world) {
                    return [
                    'id'=>$world->id,
                    'title'=>Str::words($world->title,7),
                    'short_title'=>Str::words($world->short_title,7),
                    'image_raw'=>$world->image,
                    'permalink'=>$this->url.'/'.$world->cat_slug.'/'.$world->slug,
                    'created_at'=>$world->created_at         
                    ];
                });

            $world_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $worldCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($world_f) {
                return [
                'id'=>$world_f->id,
                'title'=>Str::words($world_f->title,7),
                'short_title'=>Str::words($world_f->short_title,7),
                'image_raw'=>$world_f->image,
                'permalink'=>$this->url.'/'.$world_f->cat_slug.'/'.$world_f->slug,
                'created_at'=>$world_f->created_at    
                ];
            });
        }

        // Editorial
        $editorialCategoryId = (int)config('config.category.editorial');
        $editorialCategory = Category::with('translations')
            ->where('id', '=', $editorialCategoryId)
            ->first(); 

        if ($editorialCategory) {
            $editorialFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $editorialCategoryId)
            ->first();

            $editorial = $this->getPosts($locale)
                ->where('category_id', '=', $editorialCategoryId)
                ->where('id', '!=', $editorialFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($editorial) {
                    return [
                    'id'=>$editorial->id,
                    'title'=>Str::words($editorial->title,7),
                    'short_title'=>Str::words($editorial->short_title,7),
                    'image_raw'=>$editorial->image,
                    'permalink'=>$this->url.'/'.$editorial->cat_slug.'/'.$editorial->slug,
                    'created_at'=>$editorial->created_at         
                    ];
                });

            $editorial_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $editorialCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($editorial_f) {
                return [
                'id'=>$editorial_f->id,
                'title'=>Str::words($editorial_f->title,7),
                'short_title'=>Str::words($editorial_f->short_title,7),
                'image_raw'=>$editorial_f->image,
                'permalink'=>$this->url.'/'.$editorial_f->cat_slug.'/'.$editorial_f->slug,
                'created_at'=>$editorial_f->created_at    
                ];
            });
        }

        // Views
        $viewsCategoryId = (int)config('config.category.views');
        $viewsCategory = Category::with('translations')
            ->where('id', '=', $viewsCategoryId)
            ->first(); 

        if ($viewsCategory) {
            $viewsFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $viewsCategoryId)
            ->first();

            $views = $this->getPosts($locale)
                ->where('category_id', '=', $viewsCategoryId)
                ->where('id', '!=', $viewsFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($views) {
                    return [
                    'id'=>$views->id,
                    'title'=>Str::words($views->title,7),
                    'short_title'=>Str::words($views->short_title,7),
                    'image_raw'=>$views->image,
                    'permalink'=>$this->url.'/'.$views->cat_slug.'/'.$views->slug,
                    'created_at'=>$views->created_at         
                    ];
                });

            $views_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $viewsCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($views_f) {
                return [
                'id'=>$views_f->id,
                'title'=>Str::words($views_f->title,7),
                'short_title'=>Str::words($views_f->short_title,7),
                'image_raw'=>$views_f->image,
                'permalink'=>$this->url.'/'.$views_f->cat_slug.'/'.$views_f->slug,
                'created_at'=>$views_f->created_at    
                ];
            });
        }

        // Education
        $educationCategoryId = (int)config('config.category.education');
        $educationCategory = Category::with('translations')
            ->where('id', '=', $educationCategoryId)
            ->first(); 

        if ($educationCategory) {
            $educationFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $educationCategoryId)
            ->first();

            $education = $this->getPosts($locale)
                ->where('category_id', '=', $educationCategoryId)
                ->where('id', '!=', $educationFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($education) {
                    return [
                    'id'=>$education->id,
                    'title'=>Str::words($education->title,7),
                    'short_title'=>Str::words($education->short_title,7),
                    'image_raw'=>$education->image,
                    'permalink'=>$this->url.'/'.$education->cat_slug.'/'.$education->slug,
                    'created_at'=>$education->created_at         
                    ];
                });

            $education_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $educationCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($education_f) {
                return [
                'id'=>$education_f->id,
                'title'=>Str::words($education_f->title,7),
                'short_title'=>Str::words($education_f->short_title,7),
                'image_raw'=>$education_f->image,
                'permalink'=>$this->url.'/'.$education_f->cat_slug.'/'.$education_f->slug,
                'created_at'=>$education_f->created_at    
                ];
            });
        }

        // Scitech
        $scitechCategoryId = (int)config('config.category.scitech');
        $scitechCategory = Category::with('translations')
            ->where('id', '=', $scitechCategoryId)
            ->first(); 

        if ($scitechCategory) {
            $scitechFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $scitechCategoryId)
            ->first();

            $scitech = $this->getPosts($locale)
                ->where('category_id', '=', $scitechCategoryId)
                ->where('id', '!=', $scitechFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($scitech) {
                    return [
                    'id'=>$scitech->id,
                    'title'=>Str::words($scitech->title,7),
                    'short_title'=>Str::words($scitech->short_title,7),
                    'image_raw'=>$scitech->image,
                    'permalink'=>$this->url.'/'.$scitech->cat_slug.'/'.$scitech->slug,
                    'created_at'=>$scitech->created_at         
                    ];
                });

            $scitech_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $scitechCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($scitech_f) {
                return [
                'id'=>$scitech_f->id,
                'title'=>Str::words($scitech_f->title,7),
                'short_title'=>Str::words($scitech_f->short_title,7),
                'image_raw'=>$scitech_f->image,
                'permalink'=>$this->url.'/'.$scitech_f->cat_slug.'/'.$scitech_f->slug,
                'created_at'=>$scitech_f->created_at    
                ];
            });
        }
        
        // Health
        $healthCategoryId = (int)config('config.category.health');
        $healthCategory = Category::with('translations')
            ->where('id', '=', $healthCategoryId)
            ->first(); 

        if ($healthCategory) {
            $healthFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $healthCategoryId)
            ->first();

            $health = $this->getPosts($locale)
                ->where('category_id', '=', $healthCategoryId)
                ->where('id', '!=', $healthFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($health) {
                    return [
                    'id'=>$health->id,
                    'title'=>Str::words($health->title,7),
                    'short_title'=>Str::words($health->short_title,7),
                    'image_raw'=>$health->image,
                    'permalink'=>$this->url.'/'.$health->cat_slug.'/'.$health->slug,
                    'created_at'=>$health->created_at         
                    ];
                });

            $health_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $healthCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($health_f) {
                return [
                'id'=>$health_f->id,
                'title'=>Str::words($health_f->title,7),
                'short_title'=>Str::words($health_f->short_title,7),
                'image_raw'=>$health_f->image,
                'permalink'=>$this->url.'/'.$health_f->cat_slug.'/'.$health_f->slug,
                'created_at'=>$health_f->created_at    
                ];
            });
        }

        // Sports
        $sportsCategoryId = (int)config('config.category.sports');
        $sportsCategory = Category::with('translations')
            ->where('id', '=', $sportsCategoryId)
            ->first(); 

        if ($sportsCategory) {
            $sportsFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $sportsCategoryId)
            ->first();

            $sports = $this->getPosts($locale)
                ->where('category_id', '=', $sportsCategoryId)
                ->where('id', '!=', $sportsFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($sports) {
                    return [
                    'id'=>$sports->id,
                    'title'=>Str::words($sports->title,7),
                    'short_title'=>Str::words($sports->short_title,7),
                    'image_raw'=>$sports->image,
                    'permalink'=>$this->url.'/'.$sports->cat_slug.'/'.$sports->slug,
                    'created_at'=>$sports->created_at         
                    ];
                });

            $sports_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $sportsCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($sports_f) {
                return [
                'id'=>$sports_f->id,
                'title'=>Str::words($sports_f->title,7),
                'short_title'=>Str::words($sports_f->short_title,7),
                'image_raw'=>$sports_f->image,
                'permalink'=>$this->url.'/'.$sports_f->cat_slug.'/'.$sports_f->slug,
                'created_at'=>$sports_f->created_at    
                ];
            });
        }

        // Entertainment
        $entertainmentCategoryId = (int)config('config.category.entertainment');
        $entertainmentCategory = Category::with('translations')
            ->where('id', '=', $entertainmentCategoryId)
            ->first(); 

        if ($entertainmentCategory) {
            $entertainmentFeatured = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $entertainmentCategoryId)
            ->first();

            $entertainment = $this->getPosts($locale)
                ->where('category_id', '=', $entertainmentCategoryId)
                ->where('id', '!=', $entertainmentFeatured?->id)
                ->limit(2)
                ->get()
                ->map(function ($entertainment) {
                    return [
                    'id'=>$entertainment->id,
                    'title'=>Str::words($entertainment->title,7),
                    'short_title'=>Str::words($entertainment->short_title,7),
                    'image_raw'=>$entertainment->image,
                    'permalink'=>$this->url.'/'.$entertainment->cat_slug.'/'.$entertainment->slug,
                    'created_at'=>$entertainment->created_at         
                    ];
                });

            $entertainment_f = $this->getPosts($locale)
            ->where('type', '=', 'featured')
            ->where('category_id', '=', $entertainmentCategoryId)
            ->limit(1)
            ->get()
            ->map(function ($entertainment_f) {
                return [
                'id'=>$entertainment_f->id,
                'title'=>Str::words($entertainment_f->title,7),
                'short_title'=>Str::words($entertainment_f->short_title,7),
                'image_raw'=>$entertainment_f->image,
                'permalink'=>$this->url.'/'.$entertainment_f->cat_slug.'/'.$entertainment_f->slug,
                'created_at'=>$entertainment_f->created_at    
                ];
            });
        }

        $response['response'] = 200;
        $response['slides'] = $slider;
        $response['economy'] = $economy;
        $response['economy_featured']= $economy_f;
        $response['stock'] = $stock;
        $response['stock_featured']= $stock_f;
        $response['trade'] = $trade;
        $response['trade_featured']= $trade_f;
        $response['national'] = $national;
        $response['national_featured']= $national_f;
        $response['world'] = $world;
        $response['world_featured']= $world_f;
        $response['editorial'] = $editorial;
        $response['editorial_featured']= $editorial_f;
        $response['views'] = $views;
        $response['views_featured']= $views_f;
        $response['education'] = $education;
        $response['education_featured']= $education_f;
        $response['scitech'] = $scitech;
        $response['scitech_featured']= $scitech_f;
        $response['health'] = $health;
        $response['health_featured']= $health_f;
        $response['sports'] = $sports;
        $response['sports_featured']= $sports_f;
        $response['entertainment'] = $entertainment;
        $response['entertainment_featured']= $entertainment_f;

        return response()->json($response);
    }

    public function getPage($cat, Request $request)
    {
        
        $categoryId = (int)config('config.category.'.$cat);
        // return response()->json($categoryId);
        $posts = Post::where('cat_slug',$cat)
        ->where('status','published')
        ->orWhere('cat_slug','like', $cat.'/%')
        ->orderBy('created_at','desc')
        ->limit(30)
        ->get()
        ->map(function ($posts) {
            return [
            'id'=>$posts->id,
            'title'=>Str::words($posts->title,7),
            'short_title'=>Str::words($posts->short_title,7),
            'image_raw'=>$this->url.'/public/uploads/thumbs/w400h267_'.$posts->image,
            'permalink'=>$this->url.'/'.$posts->cat_slug.'/'.$posts->slug,
            'type'=>$posts->type,
            'created_at'=>$posts->created_at,   
            ];
        });

        if($cat == 'sci-tech'){
            $cat = 'sci_tech';
        }
        
        $seclead = Content::SectionLead($posts);
        $posts = Content::generalPosts($posts);
        $response['response'] = 200;
        $response['featured'] = $seclead;
        $response["$cat"] = $posts;
        return response()->json($response);
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getPosts($locale): Builder
    {
        return Post::with([
            'translations',
            'category',
            'category.translations',
        ])
        ->whereHas('translations', function (Builder $q) use ($locale) {
            $q->where('locale', '=', $locale);
        })
        ->where('status', '=', 1)
        ->where('datetime', '<=', now())  
        // ->whereDate('datetime', '<=', now())
        ->orderByDesc('datetime');
    }
}
