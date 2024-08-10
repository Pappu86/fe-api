<?php

namespace App\Http\Controllers\Common;

use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SettingController extends Controller
{
    /**
     * Get all key value of settings.
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getSettings(): JsonResponse
    {
        $this->authorize('view setting');

        $collections = DB::table('settings')
            ->select('id', 'key', 'label', 'value', 'updated_at', 'category', 'type', 'status')
            ->get();

        $settings = collect($collections)->groupBy('category')->toArray();

        return response()->json($settings);
    }

    /**
     * Update setting.
     *
     * @param Request $request
     * @param Setting $setting
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException|Throwable
     */
    public function update(Request $request, Setting $setting): JsonResponse
    {
        $this->authorize('update setting');

        $data = $this->validate($request, [
            'label' => 'required',
            'value' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $setting->update($data);

            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Update image settings.
     *
     * @param Request $request
     * @param Setting $setting
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException|Throwable
     */
    public function updateImage(Request $request, Setting $setting): JsonResponse
    {
        $this->authorize('update setting');

        $this->validate($request, [
            'logo' => 'nullable|image',
            'small_logo' => 'nullable|image',
            'favicon' => 'nullable|mimes:ico,png',
            'label' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $key = $request->get('key');
            $file = $request->file($key);
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $name = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $setting->addMedia($file)
                ->usingName($name)
                ->usingFileName($name . '.' . $extension)
                ->toMediaCollection($key);

            $image = $setting->getFirstMediaUrl($key);

            $setting->update([
                'label' => $request->get('label'),
                'value' => $image
            ]);

            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);

        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Update file setting.
     *
     * @param Request $request
     * @param Setting $setting
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException|Throwable
     */
    public function updateFile(Request $request, Setting $setting): JsonResponse
    {
        $this->authorize('update setting');
        $key = $request->get('key');

        $this->validate($request, [
            $key => 'nullable|file|mimetypes:application/json,text/plain',
            'label' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $file = $request->file($key);
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $name = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
            $setting->addMedia($file)
                ->usingName($name)
                ->usingFileName($name . '.' . $extension)
                ->toMediaCollection($key);

            $setting->update([
                'label' => $request->get('label'),
                'value' => $setting->getFirstMediaUrl($key)
            ]);
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);

        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();

            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}
