<?php

namespace App\Http\Controllers;

use App\Enums\ArticleCategory;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * List the published articles, optionally filtered by category (`?category=tech`).
     *
     * The blog does not exist (404) until at least one article is published. The newest featured
     * article of the selection is highlighted at the top.
     */
    public function index(Request $request): View
    {
        abort_unless(Article::hasPublished(), 404);

        $activeCategory = ArticleCategory::tryFrom((string) $request->query('category'));

        $articles = Article::query()
            ->published()
            ->when($activeCategory, fn ($query) => $query->where('category', $activeCategory))
            ->latestPublished()
            ->get();

        $featured = $articles->firstWhere('is_featured', true);

        return view('blog.index', [
            'activeCategory' => $activeCategory,
            'featured' => $featured,
            'articles' => $featured ? $articles->reject(fn (Article $article): bool => $article->is($featured))->values() : $articles,
        ]);
    }

    /**
     * Show a published article.
     */
    public function show(Article $article): View
    {
        abort_unless($article->isPublished(), 404);

        return view('blog.show', [
            'article' => $article,
            'related' => Article::query()
                ->published()
                ->whereKeyNot($article->getKey())
                ->where('category', $article->category)
                ->latestPublished()
                ->limit(3)
                ->get(),
        ]);
    }
}
