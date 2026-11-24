<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeDownloadController extends Controller
{
    /**
     * Download the resume PDF in the given language. The download is counted in GA4 by the
     * "resume_download" event the link sends (resources/js/app.js).
     */
    public function __invoke(string $locale): StreamedResponse
    {
        $path = config("site.resumes.{$locale}");

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path);
    }
}
