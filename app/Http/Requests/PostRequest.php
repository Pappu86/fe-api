<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $translation = DB::table('post_translations')
            ->where('post_id', '=', $this->id)
            ->where('locale', '=', app()->getLocale())
            ->first();

        return [
            'title' => 'required|min:5',
            'slug' => 'required|alpha_dash|unique:post_translations,slug,' . $translation?->id,
            'datetime' => 'required',
            // 'excerpt' => 'required|min:10|max:300',
            'content' => 'required|min:10',
            'type' => 'required',
        ];
    }
}
