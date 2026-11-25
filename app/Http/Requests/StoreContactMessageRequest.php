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
     * Shortest phone accepted, country code included.
     */
    public const PHONE_MIN_DIGITS = 8;

    /**
     * Longest phone accepted, country code included (E.164 limit).
     */
    public const PHONE_MAX_DIGITS = 15;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Remove the phone mask (spaces, "+", dashes, dots and parentheses) so only its digits are
     * validated and saved. Any other character is kept so the digits rule rejects it.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $this->merge(['phone' => preg_replace('/[\s+\-.()]/', '', (string) $this->input('phone'))]);
        }
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
            'phone' => ['nullable', 'digits_between:'.self::PHONE_MIN_DIGITS.','.self::PHONE_MAX_DIGITS],
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
