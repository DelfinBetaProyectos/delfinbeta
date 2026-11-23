<?php

namespace App\Models;

use App\Enums\ContactTopic;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A message sent from the home contact form. Kept so nothing is lost if an email fails.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property ContactTopic $topic
 * @property string $message
 * @property string $locale
 * @property string|null $page
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'topic', 'message', 'locale', 'page', 'read_at'])]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'topic' => ContactTopic::class,
            'read_at' => 'datetime',
        ];
    }

    /**
     * Whether the message has been read in the dashboard.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark the message as read, keeping the first time it was read.
     */
    public function markAsRead(): void
    {
        if (! $this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark the message as unread again.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Messages not read yet.
     *
     * @param  Builder<ContactMessage>  $query
     */
    #[Scope]
    protected function unread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    /**
     * First name for greetings and subjects ("Ana" from "Ana Pérez").
     */
    public function firstName(): string
    {
        return Str::before(Str::squish($this->name), ' ');
    }

    /**
     * Mailto link to answer the sender, with the notification subject prefixed by "Re:".
     */
    public function replyMailto(): string
    {
        $subject = __('emails.received.subject', [
            'tag' => __("emails.received.tags.{$this->topic->value}", [], 'es'),
            'name' => $this->firstName(),
        ], 'es');

        return 'mailto:'.$this->email.'?subject='.rawurlencode("Re: {$subject}");
    }

    /**
     * Short reference shown in the emails, e.g. "#000123".
     */
    public function reference(): string
    {
        return '#'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
