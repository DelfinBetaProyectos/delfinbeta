<?php

namespace App\Http\Requests;

use App\Enums\ContactTopic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * Name of the hidden field that only bots fill in.
     */
    public const HONEYPOT_FIELD = 'website';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'topic' => ['required', Rule::enum(ContactTopic::class)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Whether the honeypot was filled in, which only bots do.
     */
    public function isSpam(): bool
    {
        return $this->filled(self::HONEYPOT_FIELD);
    }

    /**
     * Send validation errors back to the contact section instead of the top of the page.
     */
    protected function getRedirectUrl(): string
    {
        return parent::getRedirectUrl().'#'.__('nav.sections.contact.anchor');
    }
}
