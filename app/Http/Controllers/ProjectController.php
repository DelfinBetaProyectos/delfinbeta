<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * List every published project.
     */
    public function index(): View
    {
        return view('projects.index', [
            'projects' => Project::query()->published()->ordered()->get(),
        ]);
    }

    /**
     * Show a published project's case study, linking to its neighbours (wrapping around the list).
     */
    public function show(string $slug): View
    {
        $projects = Project::query()->published()->ordered()->get(['id', 'slug', 'title'])->values();

        $position = $projects->search(fn (Project $project): bool => $project->slug === $slug);

        abort_if($position === false, 404);

        $count = $projects->count();

        return view('projects.show', [
            'project' => Project::query()->findOrFail($projects[$position]->id),
            'previous' => $projects[($position - 1 + $count) % $count],
            'next' => $projects[($position + 1) % $count],
        ]);
    }
}
