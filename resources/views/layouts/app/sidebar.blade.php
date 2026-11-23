<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-line-subtle bg-navy">
            <flux:sidebar.header class="dark">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="dark">
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="briefcase" :href="route('dashboard.projects.index')" :current="request()->routeIs('dashboard.projects.*')" wire:navigate>
                        {{ __('Projects') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="newspaper" :href="route('dashboard.articles.index')" :current="request()->routeIs('dashboard.articles.*')" wire:navigate>
                        {{ __('Articles') }}
                    </flux:sidebar.item>

                    @php($unreadMessages = App\Models\ContactMessage::query()->unread()->count())
                    {{-- The badge follows the messages page live through the "unread-messages-updated" event --}}
                    <flux:sidebar.item
                        icon="inbox"
                        :href="route('dashboard.messages.index')"
                        :current="request()->routeIs('dashboard.messages.*')"
                        wire:navigate
                        x-data="{ unread: {{ $unreadMessages }} }"
                        x-on:unread-messages-updated.window="unread = $event.detail.count"
                        badge-color="sky"
                        badge:x-show="unread > 0"
                        :badge:style="$unreadMessages > 0 ? '' : 'display: none'"
                    >
                        {{ __('Messages') }}

                        <x-slot:badge><span x-text="unread">{{ $unreadMessages }}</span></x-slot:badge>
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="envelope" :href="route('dashboard.mails.index')" :current="request()->routeIs('dashboard.mails.*')" wire:navigate>
                        {{ __('Mails') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->full_name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->full_name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->full_name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
