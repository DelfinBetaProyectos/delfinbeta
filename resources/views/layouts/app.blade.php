<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Top Nav -->
        <livewire:nav-top />

        <!-- Sidebar -->
        <livewire:sidebar />

        <!-- Main Content -->
        <div class="p-4 md:ml-64 h-auto pt-20">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="mb-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="py-6 px-4 sm:px-6 lg:px-8 text-2xl font-medium text-gray-900">
                        {{ $header }}
                    </h1>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('modals')

    @livewireScripts
</body>
</html>
