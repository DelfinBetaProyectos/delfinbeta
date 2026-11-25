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
 * @property string|null $phone
 * @property ContactTopic $topic
 * @property string $message
 * @property string $locale
 * @property string|null $page
 * @property Carbon|null $read_at
 * @property Carbon|null $responded_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'phone', 'topic', 'message', 'locale', 'page', 'read_at', 'responded_at'])]
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
            'responded_at' => 'datetime',
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
     * Whether the owner already replied to the sender.
     */
    public function isResponded(): bool
    {
        return $this->responded_at !== null;
    }

    /**
     * Record the reply now (it also counts as read), keeping the first reply time.
     */
    public function markAsResponded(): void
    {
        if (! $this->isResponded()) {
            $this->update(['responded_at' => now(), 'read_at' => $this->read_at ?? now()]);
        }
    }

    /**
     * Undo a reply recorded by mistake.
     */
    public function markAsNotResponded(): void
    {
        $this->update(['responded_at' => null]);
    }

    /**
     * Project inquiries are copied to AraguaneyCode when they arrive, so they need no reply here.
     */
    public function isForwardedToAraguaneyCode(): bool
    {
        return $this->topic->isProject();
    }

    /**
     * Whole hours between the message and the reply, or null without a reply.
     */
    public function responseHours(): ?int
    {
        return $this->responded_at === null ? null : (int) floor($this->created_at->diffInHours($this->responded_at));
    }

    /**
     * Whole hours left of the promised reply time (`site.reply_within_hours`), never below zero.
     */
    public function replyHoursLeft(): int
    {
        $elapsedHours = (int) floor($this->created_at->diffInHours(now()));

        return max(0, config('site.reply_within_hours') - $elapsedHours);
    }

    /**
     * Whether the promised reply time was kept: forwarded to AraguaneyCode, or answered in time.
     */
    public function wasHandledOnTime(): bool
    {
        return $this->isForwardedToAraguaneyCode()
            || ($this->isResponded() && $this->responseHours() <= config('site.reply_within_hours'));
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
     * Phone in international notation for display ("+584243172126"), or null without a phone.
     */
    public function displayPhone(): ?string
    {
        return $this->phone === null ? null : '+'.$this->phone;
    }

    /**
     * WhatsApp chat link for the sender's phone, or null without a phone.
     */
    public function whatsappUrl(): ?string
    {
        return $this->phone === null ? null : 'https://wa.me/'.$this->phone;
    }

    /**
     * Short reference shown in the emails, e.g. "#000123".
     */
    public function reference(): string
    {
        return '#'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
