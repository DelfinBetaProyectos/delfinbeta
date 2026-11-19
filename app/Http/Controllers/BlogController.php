<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * List articles from the static, per-locale post list, optionally filtered by pillar (`?pillar=tech`).
     */
    public function index(Request $request): View
    {
        /** @var array<string, array{label: string, filter: string}> $pillars */
        $pillars = __('blog.pillars');

        $activePillar = $request->query('pillar');
        $activePillar = is_string($activePillar) && isset($pillars[$activePillar]) ? $activePillar : null;

        /** @var Collection<string, array<string, mixed>> $posts */
        $posts = collect(__('posts.items'))
            ->when($activePillar, fn (Collection $posts): Collection => $posts->where('pillar', $activePillar));

        return view('blog.index', [
            'pillars' => $pillars,
            'activePillar' => $activePillar,
            'featured' => $posts->filter(fn (array $post): bool => $post['featured'] ?? false)->take(1),
            'posts' => $posts->reject(fn (array $post): bool => $post['featured'] ?? false),
        ]);
    }
}
