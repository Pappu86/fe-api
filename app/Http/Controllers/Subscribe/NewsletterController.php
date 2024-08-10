<?php

namespace App\Http\Controllers\Subscribe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Http\Resources\Subscribers\SubscriberResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getSubscribers(Request $request)
    {
        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $subscribes = Subscriber::latest();

        if(isset($query)){
            $subscribes=$subscribes->where('email', "LIKE", "%" . $query . "%");                                        
        }      

        if ($per_page === '-1') {
            $results = $subscribes->get();
            $subscribes = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $subscribes = $subscribes->paginate($per_page);
        }
        return SubscriberResource::collection($subscribes);
    }
}
