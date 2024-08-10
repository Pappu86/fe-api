<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Lang;
use Throwable;

class CacheManagementController extends Controller
{
    /**
     * Get all supported artisan commands.
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getArtisanCommands(): JsonResponse|AnonymousResourceCollection
    {
        try {
            $commands = collect();
            foreach (config('config.artisan_commands') as $command => $details) {
                $commands->push([
                    'key' => $command,
                    'text' => $details['text'],
                    'class' => $details['class']
                ]);
            }

            return response()->json($commands);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Run artisan command.
     *
     * @param $command
     * @return JsonResponse
     */
    public function runArtisanCommand($command): JsonResponse
    {
        try {
            Artisan::call($command);

            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}
