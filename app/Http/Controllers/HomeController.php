<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * How many projects the home page shows; the rest live on the projects page.
     */
    public const PROJECTS_LIMIT = 3;

    /**
     * Show the home page with the first published projects.
     */
    public function __invoke(): View
    {
        $projects = Project::query()->published()->ordered()->limit(self::PROJECTS_LIMIT + 1)->get();

        return view('home', [
            'projects' => $projects->take(self::PROJECTS_LIMIT),
            'hasMoreProjects' => $projects->count() > self::PROJECTS_LIMIT,
        ]);
    }
}
