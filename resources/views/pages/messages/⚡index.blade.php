<?php

use App\Enums\ContactTopic;
use App\Models\ContactMessage;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Messages')] class extends Component {
    use WithPagination;

    /**
     * How many messages each page lists.
     */
    public const PER_PAGE = 20;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $topic = '';

    /**
     * "unread", "read" or "" for every message.
     */
    #[Url(except: '')]
    public string $status = '';

    public ?int $viewingMessageId = null;

    public ?int $deletingMessageId = null;

    /**
     * Go back to the first page whenever the search or the topic filter changes.
     */
    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'topic', 'status'], true)) {
            $this->resetPage();
        }
    }

    /**
     * Messages from the contact form, newest first, filtered by the search and the topic.
     *
     * @return LengthAwarePaginator<int, ContactMessage>
     */
    #[Computed]
    public function messages(): LengthAwarePaginator
    {
        $topic = ContactTopic::tryFrom($this->topic);
        $search = trim($this->search);

        return ContactMessage::query()
            ->when($topic, fn ($query) => $query->where('topic', $topic))
            ->when($this->status === 'unread', fn ($query) => $query->unread())
            ->when($this->status === 'read', fn ($query) => $query->whereNotNull('read_at'))
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('email', "%{$search}%")
                ->orWhereLike('message', "%{$search}%")))
            ->latest('id')
            ->paginate(self::PER_PAGE);
    }

    /**
     * The message open in the detail modal.
     */
    #[Computed]
    public function viewingMessage(): ?ContactMessage
    {
        return $this->viewingMessageId ? ContactMessage::query()->find($this->viewingMessageId) : null;
    }

    /**
     * How many messages are still unread, shown next to the heading.
     */
    #[Computed]
    public function unreadCount(): int
    {
        return ContactMessage::query()->unread()->count();
    }

    /**
     * Open a message in the detail modal; opening it marks it as read.
     */
    public function openMessage(int $messageId): void
    {
        $contactMessage = ContactMessage::query()->findOrFail($messageId);
        $contactMessage->markAsRead();

        $this->viewingMessageId = $contactMessage->id;
        $this->refreshCounts();

        Flux::modal('contact-message')->show();
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(int $messageId): void
    {
        ContactMessage::query()->findOrFail($messageId)->markAsRead();
        $this->refreshCounts();

        Flux::toast(variant: 'success', text: __('Message marked as read.'));
    }

    /**
     * Mark a message as unread again (and close it if it is open).
     */
    public function markAsUnread(int $messageId): void
    {
        ContactMessage::query()->findOrFail($messageId)->markAsUnread();
        $this->refreshCounts();

        if ($this->viewingMessageId === $messageId) {
            $this->viewingMessageId = null;
            Flux::modal('contact-message')->close();
        }

        Flux::toast(variant: 'success', text: __('Message marked as unread.'));
    }

    /**
     * Record that the sender was answered, for the response time in the statistics.
     */
    public function markAsResponded(int $messageId): void
    {
        ContactMessage::query()->findOrFail($messageId)->markAsResponded();
        $this->refreshCounts();

        Flux::toast(variant: 'success', text: __('Message marked as replied.'));
    }

    /**
     * Undo a reply recorded by mistake.
     */
    public function markAsNotResponded(int $messageId): void
    {
        ContactMessage::query()->findOrFail($messageId)->markAsNotResponded();
        $this->refreshCounts();

        Flux::toast(variant: 'success', text: __('Reply removed.'));
    }

    /**
     * Mark every unread message as read.
     */
    public function markAllAsRead(): void
    {
        ContactMessage::query()->unread()->update(['read_at' => now()]);
        $this->refreshCounts();

        Flux::toast(variant: 'success', text: __('All messages marked as read.'));
    }

    /**
     * Ask for confirmation before deleting a message.
     */
    public function confirmDelete(int $messageId): void
    {
        $this->deletingMessageId = ContactMessage::query()->findOrFail($messageId)->id;

        Flux::modal('contact-message')->close();
        Flux::modal('delete-message')->show();
    }

    /**
     * Delete the message awaiting confirmation.
     */
    public function delete(): void
    {
        ContactMessage::query()->findOrFail($this->deletingMessageId)->delete();

        if ($this->viewingMessageId === $this->deletingMessageId) {
            $this->viewingMessageId = null;
        }

        $this->deletingMessageId = null;
        $this->refreshCounts();

        Flux::modal('delete-message')->close();
        Flux::toast(variant: 'success', text: __('Message deleted.'));
    }

    /**
     * Clear the search and the filters.
     */
    public function clearFilters(): void
    {
        $this->reset('search', 'topic', 'status');
        $this->resetPage();
    }

    /**
     * Recompute the list and the unread count after a change, and update the sidebar badge.
     */
    private function refreshCounts(): void
    {
        unset($this->messages, $this->unreadCount, $this->viewingMessage);

        $this->dispatch('unread-messages-updated', count: $this->unreadCount);
    }

    /**
     * When a message was sent, in Maracay time and the dashboard language.
     */
    public function sentAt(ContactMessage $contactMessage): string
    {
        return $contactMessage->created_at->locale(app()->getLocale())->isoFormat('D MMM YYYY, h:mm a');
    }
}; ?>

<section class="w-full">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl" level="1" class="flex items-center gap-2">
                {{ __('Messages') }}
                @if ($this->unreadCount > 0)
                    <flux:badge color="sky" size="sm">{{ trans_choice(':count unread|:count unread', $this->unreadCount) }}</flux:badge>
                @endif
            </flux:heading>
            <flux:subheading size="lg">{{ __('Messages sent from the contact form on the home page') }}</flux:subheading>
        </div>

        @if ($this->unreadCount > 0)
            <flux:button icon="check" wire:click="markAllAsRead">{{ __('Mark all as read') }}</flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" class="mb-6" />

    <div class="mb-6 flex flex-wrap items-end gap-4">
        <div class="min-w-64 flex-1">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" :placeholder="__('Search by name, email or message')" :aria-label="__('Search')" clearable />
        </div>

        <div class="w-56">
            <flux:select wire:model.live="topic" :aria-label="__('Topic')">
                <flux:select.option value="">{{ __('All topics') }}</flux:select.option>
                @foreach (ContactTopic::cases() as $topicOption)
                    <flux:select.option :value="$topicOption->value">{{ $topicOption->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <div class="w-44">
            <flux:select wire:model.live="status" :aria-label="__('Status')">
                <flux:select.option value="">{{ __('All messages') }}</flux:select.option>
                <flux:select.option value="unread">{{ __('Unread') }}</flux:select.option>
                <flux:select.option value="read">{{ __('Read') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    @if ($this->messages->isEmpty())
        @if ($search !== '' || $topic !== '' || $status !== '')
            <flux:text>
                {{ __('No messages match these filters.') }}
                <flux:link as="button" wire:click="clearFilters">{{ __('Clear filters') }}</flux:link>
            </flux:text>
        @else
            <flux:text>{{ __('There are no messages yet.') }}</flux:text>
        @endif
    @else
        <flux:table :paginate="$this->messages">
            <flux:table.columns>
                <flux:table.column>{{ __('Date') }}</flux:table.column>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Topic') }}</flux:table.column>
                <flux:table.column>{{ __('Message') }}</flux:table.column>
                <flux:table.column>{{ __('Language') }}</flux:table.column>
                <flux:table.column><span class="sr-only">{{ __('Actions') }}</span></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->messages as $contactMessage)
                    <flux:table.row :key="$contactMessage->id" :class="$contactMessage->isRead() ? '' : 'bg-sky-50/60 dark:bg-sky-400/5'">
                        <flux:table.cell class="whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if ($contactMessage->isRead())
                                    <span class="size-2"></span>
                                @else
                                    <span class="size-2 rounded-full bg-sky-500"></span>
                                    <span class="sr-only">{{ __('Unread') }}</span>
                                @endif
                                {{ $this->sentAt($contactMessage) }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div @class(['text-zinc-800 dark:text-white', 'font-semibold' => ! $contactMessage->isRead(), 'font-medium' => $contactMessage->isRead()])>{{ $contactMessage->name }}</div>
                            <div class="text-xs">{{ $contactMessage->email }}</div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$contactMessage->topic->isProject() ? 'sky' : 'zinc'" size="sm" inset="top bottom">{{ $contactMessage->topic->label() }}</flux:badge>
                            @if ($contactMessage->isResponded())
                                <flux:badge color="green" size="sm" icon="check" inset="top bottom">{{ __('Replied') }}</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell class="max-w-md truncate">{{ Str::limit(Str::squish($contactMessage->message), 90) }}</flux:table.cell>

                        <flux:table.cell class="uppercase">{{ $contactMessage->locale }}</flux:table.cell>

                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" size="sm" icon="eye" inset="top bottom" wire:click="openMessage({{ $contactMessage->id }})">{{ __('View') }}</flux:button>

                                <flux:dropdown position="bottom" align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" :aria-label="__('Actions')" />

                                    <flux:menu>
                                        @if ($contactMessage->isRead())
                                            <flux:menu.item icon="envelope" wire:click="markAsUnread({{ $contactMessage->id }})">{{ __('Mark as unread') }}</flux:menu.item>
                                        @else
                                            <flux:menu.item icon="envelope-open" wire:click="markAsRead({{ $contactMessage->id }})">{{ __('Mark as read') }}</flux:menu.item>
                                        @endif

                                        @if ($contactMessage->isResponded())
                                            <flux:menu.item icon="arrow-uturn-left" wire:click="markAsNotResponded({{ $contactMessage->id }})">{{ __('Mark as not replied') }}</flux:menu.item>
                                        @else
                                            <flux:menu.item icon="check-circle" wire:click="markAsResponded({{ $contactMessage->id }})">{{ __('Mark as replied') }}</flux:menu.item>
                                        @endif

                                        <flux:menu.separator />

                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmDelete({{ $contactMessage->id }})">{{ __('Delete') }}</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif

    <flux:modal name="contact-message" class="w-full max-w-2xl">
        @if ($openMessage = $this->viewingMessage)
            <div class="space-y-6">
                <div>
                    <flux:badge :color="$openMessage->topic->isProject() ? 'sky' : 'zinc'" size="sm">{{ $openMessage->topic->label() }}</flux:badge>
                    <flux:heading size="lg" class="mt-3">{{ $openMessage->name }}</flux:heading>
                    <flux:text class="mt-1">
                        <flux:link :href="'mailto:'.$openMessage->email">{{ $openMessage->email }}</flux:link>
                        · {{ $this->sentAt($openMessage) }} · {{ config('site.locale_names.'.$openMessage->locale) }} · {{ $openMessage->reference() }}
                    </flux:text>
                </div>

                <div class="rounded-lg bg-zinc-50 p-4 whitespace-pre-line text-zinc-800 dark:bg-white/5 dark:text-zinc-200">{{ $openMessage->message }}</div>

                @if ($openMessage->topic->isProject())
                    <flux:text>{{ __('A copy was sent to :email.', ['email' => config('site.araguaneycode_email')]) }}</flux:text>
                @endif

                <div class="flex flex-wrap gap-2">
                    <flux:button variant="ghost" icon="trash" wire:click="confirmDelete({{ $openMessage->id }})" class="text-red-600! dark:text-red-400!">{{ __('Delete') }}</flux:button>
                    <flux:button variant="ghost" icon="envelope" wire:click="markAsUnread({{ $openMessage->id }})">{{ __('Mark as unread') }}</flux:button>

                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Close') }}</flux:button>
                    </flux:modal.close>

                    @unless ($openMessage->isResponded())
                        <flux:button icon="check-circle" wire:click="markAsResponded({{ $openMessage->id }})">{{ __('Mark as replied') }}</flux:button>
                    @endunless

                    <flux:button variant="primary" icon="envelope" :href="$openMessage->replyMailto()">{{ __('Reply') }}</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <flux:modal name="delete-message" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete message?') }}</flux:heading>
                <flux:text class="mt-2">{{ __('The message will be permanently deleted. This cannot be undone.') }}</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</section>
