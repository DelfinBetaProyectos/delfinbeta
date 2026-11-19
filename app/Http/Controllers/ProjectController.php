<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Show a project case study from the static, per-locale project list.
     */
    public function show(string $project): View
    {
        /** @var array<string, array<string, mixed>> $projects */
        $projects = __('projects.items');

        abort_unless(isset($projects[$project]), 404);

        $slugs = array_keys($projects);
        $position = array_search($project, $slugs, true);
        $previousSlug = $slugs[($position - 1 + count($slugs)) % count($slugs)];
        $nextSlug = $slugs[($position + 1) % count($slugs)];

        return view('projects.show', [
            'project' => $projects[$project],
            'slug' => $project,
            'previous' => ['slug' => $previousSlug, 'title' => $projects[$previousSlug]['title']],
            'next' => ['slug' => $nextSlug, 'title' => $projects[$nextSlug]['title']],
        ]);
    }
}
