<?php

namespace App\Http\Controllers\Rss;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\View\Factory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RssFeedController extends Controller
{
    /**
     * Get feed by category.
     *
     * @param Factory $view
     * @param ResponseFactory $response
     * @return \Illuminate\Http\Response
     */
    public function feed(Factory $view, ResponseFactory $response,)
    {
        $feed = collect();
        $posts = $this->getPosts('en')
            ->limit(5)
            ->get()  
            ->map(function ($post) {
                
                $post['slug']=$post->slug;

                if($post->category && $post->category->slug){
                    $post['slug']=$post->category->slug.'/'.$post->slug;
                }

                return $post;
            });
        $slug='home';
        $feed->title = 'The Financial Express BD News RSS Feed';
        $feed->description = 'The Financial Express (FE) is the first financial daily of Bangladesh in English under the ownership of company -- International Publications Limited which was incorporated under the Companies Act in the year, 1993. The FE started its journey in 1993. In terms of circulation this daily now ranks second, both in its print and on online editions, among its contemporaries, in English language, in Bangladesh. It has currently syndication arrangements with the London-based Financial Times (FT) and the Prague-based Project Syndicate.';
        $feed->logo = 'https://thefinancialexpress.com.bd/_nuxt/img/logo-d-l.0b8e5f9.png';
        $feed->link = url('feed/' . $slug);
        $feed->lastBuildDate = Carbon::now()->format('D, d M Y H:i:s O');
        $feed->lang = 'en';

        $items = collect();
        // Log::info("post-111", [$posts]);
        
        foreach ($posts as $post) {
            $postSlug=$post->slug;

            $item = [
                'title' => $post->short_title,
                'slug' => $postSlug,
                'author' => $post->reporter ? $post->reporter : 'FE ONLINE DESK',
                'link' => url($slug . '/' . urlencode($postSlug) . '?amp=true'),
                'published_at' => Carbon::parse($post->created_at ? $post->created_at : $post->updated_at)->format('D, d M Y H:i:s O'),
                'description' => str_replace('"', " ", strip_tags(html_entity_decode($post->content))),
                'content' => str_replace('"', " ", html_entity_decode($post->content)),
                'category' => $slug,
                'image' => $post->image ? $post->image : asset('images/financial-express.jpg'),
                'caption' => $post->caption ? $post->caption : $post->short_title
            ];
            $items->push($item);
        }
        // Log::info("post-111", [$posts]);
        $data = ['content' => $view->make('rss.rss-google', ['items' => $posts, 'feed' => $feed])->render(), 'headers' => ['Content-type' => 'application/rss+xml; charset=utf-8']];

        return $response->make($data['content'], 200, $data['headers']);
    }

    /**
     * Get feed by category.
     *
     * @param Factory $view
     * @param ResponseFactory $response
     * @param $slug
     * @return \Illuminate\Http\Response
     */
    public function feedByCategory(Factory $view, ResponseFactory $response, $slug)
    {
        $feed = collect();
        if($slug=='special-issues'){
            $categoryId = (int)config('config.category.special_issues');
        }else{
            $categoryId = (int)config('config.category.'.$slug);
        }

        switch ($slug) {
            case 'home':
                $posts = $this->getPosts('en')
                    ->whereIn('type', ['column1', 'column2','column3'])
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'economy':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'stock':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'national':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'world':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'views':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'analysis':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            case 'special-issues':
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
            default:
                $posts = $this->getPosts('en')    
                    ->where('category_id', '=', $categoryId)                
                    ->latest()
                    ->take(30)
                    ->get();
                break;
        }

        $feed->title = 'The Financial Express BD News RSS Feed';
        $feed->description = 'The Financial Express (FE) is the first financial daily of Bangladesh in English under the ownership of company -- International Publications Limited which was incorporated under the Companies Act in the year, 1993. The FE started its journey in 1993. In terms of circulation this daily now ranks second, both in its print and on online editions, among its contemporaries, in English language, in Bangladesh. It has currently syndication arrangements with the London-based Financial Times (FT) and the Prague-based Project Syndicate.';
        $feed->logo = 'https://thefinancialexpress.com.bd/_nuxt/img/logo-d-l.0b8e5f9.png';
        $feed->link = url('feed/' . $slug);
        $feed->lastBuildDate = Carbon::now()->format('D, d M Y H:i:s O');
        $feed->lang = 'en';

        $items = collect();        
        foreach ($posts as $post) {
            $postSlug=$post->slug;
            $item = [
                'title' => $post->short_title,
                'slug' => $postSlug,
                'author' => $post->reporter ? $post->reporter : 'FE ONLINE DESK',
                'link' => 'https://thefinancialexpress.com.bd/'.$slug.'/'.urlencode($postSlug). '?amp=true',
                'published_at' => Carbon::parse($post->created_at ? $post->created_at : $post->updated_at)->format('D, d M Y H:i:s O'),
                'description' => str_replace('"', " ", strip_tags(html_entity_decode($post->content))),
                'content' => str_replace('"', " ", html_entity_decode($post->content)),
                'category' => $slug,
                'image' => $post->image ? $post->image : asset('images/financial-express.jpg'),
                'caption' => $post->caption ? $post->caption : $post->short_title
            ];
            $items->push($item);
        }

        $data = ['content' => $view->make('rss.rss-google', ['items' => $items, 'feed' => $feed])->render(), 'headers' => ['Content-type' => 'application/rss+xml; charset=utf-8']];
        return $response->make($data['content'], 200, $data['headers']);
    }

    /**
     * Get feed by child category.
     *
     * @param Factory $view
     * @param ResponseFactory $response
     * @param $page
     * @param $slug
     * @return \Illuminate\Http\Response
     */
    public function feedByChildCategory(Factory $view, ResponseFactory $response, $page, $slug)
    {
        $feed = collect();
        $posts = DB::table('posts')
            ->where('status', '=', 'published')
            ->where('cat_slug', '=', $page . '/' . $slug)
            ->latest()
            ->take(20)
            ->get();

        $feed->title = 'The Financial Express BD News RSS Feed';
        $feed->description = 'The Financial Express (FE) is the first financial daily of Bangladesh in English under the ownership of company -- International Publications Limited which was incorporated under the Companies Act in the year, 1993. The FE started its journey in 1993. In terms of circulation this daily now ranks second, both in its print and on online editions, among its contemporaries, in English language, in Bangladesh. It has currently syndication arrangements with the London-based Financial Times (FT) and the Prague-based Project Syndicate.';
        $feed->logo = 'https://thefinancialexpress.com.bd/img/logo.png';
        $feed->link = url('feed/' . $page . '/' . $slug);
        $feed->lastBuildDate = Carbon::now()->format('D, d M Y H:i:s O');
        $feed->lang = 'en';

        $items = collect();
        foreach ($posts as $post) {
            $item = [
                'title' => $post->short_title,
                'slug' => $post->slug,
                'author' => $post->reporter ? $post->reporter : 'FE ONLINE DESK',
                'link' => url($page . '/' . $slug . '/' . urlencode($post->slug) . '?amp=true'),
                'published_at' => Carbon::parse($post->published_at ? $post->published_at : $post->updated_at)->format('D, d M Y H:i:s O'),
                'description' => str_replace('"', " ", strip_tags(html_entity_decode($post->content))),
                'content' => str_replace('"', " ", html_entity_decode($post->content)),
                'category' => $slug,
                'image' => $post->image ? asset("uploads/$post->image") : asset('images/financial-express.jpg'),
                'caption' => $post->caption ? $post->caption : $post->short_title
            ];
            $items->push($item);
        }

        $data = ['content' => $view->make('rss-google', ['items' => $items, 'feed' => $feed])->render(), 'headers' => ['Content-type' => 'application/rss+xml; charset=utf-8']];

        return $response->make($data['content'], 200, $data['headers']);
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
