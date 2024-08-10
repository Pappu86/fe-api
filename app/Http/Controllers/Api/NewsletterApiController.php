<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Newsletter;

class NewsletterApiController extends Controller
{
    /**
     * @OA\Post (
     *     path="/{locale}/newsletter/subscribe",
     *     summary="Newsletter subscribe.",
     *     description="Newsletter subscribe.",
     *     operationId="newsletterSubscribe",
     *     tags={"Newsletter Subscribe"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              ref="#/components/schemas/Post",
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="postId", type="integer", example="1")
     *     ),
     * ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="401",
     *     ref="#/components/responses/401"
     * ),
     *     @OA\Response (
     *     response="403",
     *     ref="#/components/responses/403"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function createSubscriber(Request $request, $locale): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);     

        // begin database transaction
        DB::beginTransaction();
        try {
            $email=$request->get('email');
            $subscribe = Subscriber::query()->where('email', $email)->first();
            $subscribeId=$subscribe?->id;

            if(isset($subscribe)){   
                return response()->json([
                    'message' => Lang::get('crud.already_subscribed'),
                    'subscribeId' => $subscribeId,
                ]);
            }else{
                $newsletter= Newsletter::subscribeOrUpdate($email);
                $newsletterId=$newsletter['id'];
                $newsletterStatus=$newsletter['status'];
                if($newsletterId){
                    $subscribe = Subscriber::create(['email'=>$email, 'mailchimp_id'=> $newsletterId, 'mailchimp_status'=>$newsletterStatus]);
                    $subscribeId=$subscribe?->id;
                }
                // commit database
                DB::commit();
                // return success message
                return response()->json([
                    'message' => Lang::get('crud.subscribed'),
                    'subscribeId' => $subscribeId
                ]);
            }
        } catch (Throwable  $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}
