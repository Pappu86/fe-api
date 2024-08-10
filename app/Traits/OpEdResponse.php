<?php

namespace App\Traits;

use App\Models\OpedPost;

trait OpEdResponse
{
    /**
     * @param $categoryId
     * @return array
     */
    protected function getOpEdPosts($categoryId): array
    {
        return OpedPost::query()
            ->where('category_id', '=', $categoryId)
            ->pluck('post_id')
            ->toArray();
    }
}
