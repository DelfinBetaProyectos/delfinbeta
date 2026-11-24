<?php

namespace App\Enums;

/**
 * The blog's categories. Labels live in lang/{locale}/blog.php under `categories`.
 */
enum ArticleCategory: string
{
    case Tech = 'tech';
    case Business = 'business';
    case Mama = 'mama';
    case Community = 'community';

    /**
     * Short label shown on article cards, e.g. "Experiencia técnica".
     */
    public function label(): string
    {
        return __("blog.categories.{$this->value}.label");
    }

    /**
     * Longer label used by the blog filter buttons.
     */
    public function filterLabel(): string
    {
        return __("blog.categories.{$this->value}.filter");
    }
}
