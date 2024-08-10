<?php

namespace App\Traits;

trait PostSlug
{
    /**
     * @param object $category
     * @param string $slug
     * @return string
     */
    protected function getSlug(object $category, string $slug): string
    {
            return '/' . $category?->slug . '/' . $slug;
    }
}
