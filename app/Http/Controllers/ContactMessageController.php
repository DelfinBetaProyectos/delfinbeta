<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactConfirmation;
use App\Mail\ContactReceived;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    /**
     * Save a message from the home contact form and queue both emails: the notification to Dayan
     * (copied to AraguaneyCode for projects) and the confirmation to the sender in their language.
     *
     * Bots that fill the honeypot get the same success message, but nothing is saved or sent.
     */
    public function __invoke(StoreContactMessageRequest $request): RedirectResponse
    {
        $contactAnchor = __('nav.sections.contact.anchor');

        if (! $request->isSpam()) {
            $contactMessage = ContactMessage::create([
                ...$request->validated(),
                'locale' => App::getLocale(),
                'page' => (parse_url(url()->previous(), PHP_URL_PATH) ?: '/').'#'.$contactAnchor,
            ]);

            Mail::send(new ContactReceived($contactMessage));
            Mail::send(new ContactConfirmation($contactMessage));
        }

        return redirect()->to(route('home').'#'.$contactAnchor)->with('contact_sent', true);
    }
}
