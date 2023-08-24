<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <nav id="menu" class="sticky top-0 z-20 px-4 lg:px-6 py-2.5 dark:bg-delfinbeta-dark">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
            <a href="{{ route('welcome') }}" class="flex items-center">
                <img id="logo" src="{{ asset('img/DelfinBeta_White.png') }}" class="mr-3 h-14 md:h-24" alt="DelfinBeta" title="DelfinBeta" />
            </a>
            @if (Route::has('login'))
                <div class="flex items-center lg:order-2">
                    @auth
                        <x-button-link-outline href="{{ route('dashboard') }}">{{ Auth::user()->name }}</x-button-link-outline>
                    @else
                        <x-button-link-outline href="{{ route('login') }}">Iniciar Sesión</x-button-link-outline>

                        @if (Route::has('register'))
                            <x-button-link href="{{ route('register') }}">Registro</x-button-link>
                        @endif
                    @endauth
                    
                    <button data-collapse-toggle="mobile-menu-2" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="mobile-menu-2" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                        <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
            @endif
            <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1" id="mobile-menu-2">
                <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                    <li>
                        <x-nav-link href="{{ route('welcome') }}" :active="request()->routeIs('welcome')">Inicio</x-nav-link>
                    </li>
                    <li>
                        <x-nav-link href="#about">Sobre mi</x-nav-link>
                    </li>
                    <li>
                        <x-nav-link href="#skills">Habilidades</x-nav-link>
                    </li>
                    <li>
                        <x-nav-link href="#blog">Blog</x-nav-link>
                    </li>
                    <!-- <li>
                        <x-nav-link href="#">Portafolio</x-nav-link>
                    </li> -->
                    <li>
                        <x-nav-link href="#contact">Contacto</x-nav-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="bg-white dark:bg-delfinbeta-dark">
        <div class="py-8 lg:py-24 px-4 lg:px-12 mx-auto max-w-screen-xl text-center">
            <!-- <a href="#" class="inline-flex justify-between items-center py-1 px-1 pr-4 mb-7 text-sm text-gray-700 bg-gray-100 rounded-full dark:bg-gray-800 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700" role="alert">
                <span class="text-xs bg-delfinbeta-medium_dark hover:bg-delfinbeta-medium rounded-full text-white px-4 py-1.5 mr-3">Nuevo</span> 
                <span class="text-sm font-medium">Artículo del Blog</span> 
                <svg class="ml-2 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </a> -->
            <h1 class="mb-7 text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                Desarrollo Web - Frontend y Backend
            </h1>
            <p class="mb-7 text-lg font-normal text-gray-500 lg:text-xl sm:px-16 xl:px-48 dark:text-gray-400">
                ¡Hola! Soy Dayan Betancourt #MamaDeveloper
            </p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
                <x-button-link href="{{ route('login') }}">
                    Conóceme
                    <svg class="inline ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </x-button-link>
                <x-button-link-outline href="{{ route('login') }}">
                    <svg class="inline mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/>
                        <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
                    </svg>
                    Escríbeme
                </x-button-link-outline>
            </div>
        </div>
    </header>

    <!-- Sobre mi -->
    <section id="about" class="bg-white dark:bg-gray-900">
        <div class="py-12 lg:py-28 px-4 lg:px-6 mx-auto max-w-screen-xl items-center lg:grid lg:gap-16 lg:grid-cols-2">
            <div class="font-light text-gray-500 sm:text-lg dark:text-gray-400">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">¡Hola chamos! yo soy @delfinbeta</h2>
                <p class="mb-4">@delfinbeta es mi apodo en redes sociales, mi nombre es Dayan Betancourt 👧🏾. Me dedico al desarrollo web Frontend y Backend. Fundé Tecno D 2.0 un pequeño emprendimiento y soy Líder de la la comunidad de tecnología H/F Maracay. Y soy orgullosamente Venezolana.</p>
                <p class="mb-4">💻 Actualmente utilizo PHP bajo el framework Laravel.</p>
                <p class="mb-4">📘 Estoy interesada en aprender sobre Frontend (Js - Vue - React) y Transformación Digital.</p>
                <p class="mb-4">💬 Puedes preguntarme sobre HTML, CSS, PHP y Laravel</p>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-8">
                <img src="{{ asset('img/Dayan_Betancourt.jpg') }}" alt="Dayan Betancourt" title="Dayan Betancourt" class="w-full rounded-lg" />
                <img src="{{ asset('img/Stickers.jpg') }}" alt="Stickers" title="Stickers" class="mt-4 w-full lg:mt-10 rounded-lg" />
            </div>
        </div>
    </section>

    <!-- Habilidades -->
    <section id="skills" class="bg-white dark:bg-delfinbeta-medium_dark">
        <div class="py-12 lg:py-32 px-4 lg:px-6 mx-auto max-w-screen-xl">
            <div class="max-w-screen-md mb-8 lg:mb-16">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Mis Habilidades</h2>
                <p class="text-gray-500 sm:text-xl dark:text-gray-400">
                    Tengo experiencia en las siguientes áreas de conocimiento:
                </p>
            </div>
            <div class="space-y-8 md:grid md:grid-cols-3 lg:grid-cols-5 md:gap-12 md:space-y-0">
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M.906 0 2.5 17.683l7.5 2.159 7.544-2.158L19.092 0H.906ZM15.1 6H7.044l.174 2h7.776l-.632 6.513-4.29 1.371-4.326-1.444-.29-2.909H7.5l.163 1.4 2.424.809 2.37-.757.3-2.982H5.368L4.8 4h10.519L15.1 6Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">HTML</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M.906 0 2.5 17.781l7.5 2.16 7.544-2.159L19.091 0H.906Zm13.437 14.679-4.337 1.2h-.009l-4.341-1.2-.3-3.158h2.13l.151 1.521 2.36.637 2.363-.638.248-3.041H5.264l-.19-2h7.718l.177-2H4.87l-.177-2h10.614l-.964 10.679Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">CSS</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512">
                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path d="M0 32v448h448V32H0zm243.8 349.4c0 43.6-25.6 63.5-62.9 63.5-33.7 0-53.2-17.4-63.2-38.5l34.3-20.7c6.6 11.7 12.6 21.6 27.1 21.6 13.8 0 22.6-5.4 22.6-26.5V237.7h42.1v143.7zm99.6 63.5c-39.1 0-64.4-18.6-76.7-43l34.3-19.8c9 14.7 20.8 25.6 41.5 25.6 17.4 0 28.6-8.7 28.6-20.8 0-14.4-11.4-19.5-30.7-28l-10.5-4.5c-30.4-12.9-50.5-29.2-50.5-63.5 0-31.6 24.1-55.6 61.6-55.6 26.8 0 46 9.3 59.8 33.7L368 290c-7.2-12.9-15-18-27.1-18-12.3 0-20.1 7.8-20.1 18 0 12.6 7.8 17.7 25.9 25.6l10.5 4.5c35.8 15.3 55.9 31 55.9 66.2 0 37.8-29.8 58.6-69.7 58.6z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Javascript</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512">
                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path d="M333.5,201.4c0-22.1-15.6-34.3-43-34.3h-50.4v71.2h42.5C315.4,238.2,333.5,225,333.5,201.4z M517,188.6 c-9.5-30.9-10.9-68.8-9.8-98.1c1.1-30.5-22.7-58.5-54.7-58.5H123.7c-32.1,0-55.8,28.1-54.7,58.5c1,29.3-0.3,67.2-9.8,98.1 c-9.6,31-25.7,50.6-52.2,53.1v28.5c26.4,2.5,42.6,22.1,52.2,53.1c9.5,30.9,10.9,68.8,9.8,98.1c-1.1,30.5,22.7,58.5,54.7,58.5h328.7 c32.1,0,55.8-28.1,54.7-58.5c-1-29.3,0.3-67.2,9.8-98.1c9.6-31,25.7-50.6,52.1-53.1v-28.5C542.7,239.2,526.5,219.6,517,188.6z M300.2,375.1h-97.9V136.8h97.4c43.3,0,71.7,23.4,71.7,59.4c0,25.3-19.1,47.9-43.5,51.8v1.3c33.2,3.6,55.5,26.6,55.5,58.3 C383.4,349.7,352.1,375.1,300.2,375.1z M290.2,266.4h-50.1v78.4h52.3c34.2,0,52.3-13.7,52.3-39.5 C344.7,279.6,326.1,266.4,290.2,266.4z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Bootstrap</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.782.72a4.773 4.773 0 0 0-4.8 4.173 3.43 3.43 0 0 1 2.741-1.687c1.689 0 2.974 1.972 3.758 2.587a5.733 5.733 0 0 0 5.382.935c2-.638 2.934-2.865 3.137-3.921-.969 1.379-2.44 2.207-4.259 1.231C14.488 3.365 13.551.6 9.782.72ZM4.8 6.979A4.772 4.772 0 0 0 0 11.151a3.43 3.43 0 0 1 2.745-1.687c1.689 0 2.974 1.972 3.758 2.587a5.733 5.733 0 0 0 5.382.935c2-.638 2.933-2.865 3.137-3.921-.97 1.379-2.44 2.208-4.259 1.231C9.51 9.623 8.573 6.853 4.8 6.979Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Tailwind</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 5.625c4.418 0 8-1.063 8-2.375S12.418.875 8 .875 0 1.938 0 3.25s3.582 2.375 8 2.375Zm0 13.5c4.963 0 8-1.538 8-2.375v-4.019c-.052.029-.112.054-.165.082a8.08 8.08 0 0 1-.745.353c-.193.081-.394.158-.6.231l-.189.067c-2.04.628-4.165.936-6.3.911a20.601 20.601 0 0 1-6.3-.911l-.189-.067a10.719 10.719 0 0 1-.852-.34 8.08 8.08 0 0 1-.493-.244c-.053-.028-.113-.053-.165-.082v4.019C0 17.587 3.037 19.125 8 19.125Zm7.09-12.709c-.193.081-.394.158-.6.231l-.189.067a20.6 20.6 0 0 1-6.3.911 20.6 20.6 0 0 1-6.3-.911l-.189-.067a10.719 10.719 0 0 1-.852-.34 8.08 8.08 0 0 1-.493-.244C.112 6.035.052 6.01 0 5.981V10c0 .837 3.037 2.375 8 2.375s8-1.538 8-2.375V5.981c-.052.029-.112.054-.165.082a8.08 8.08 0 0 1-.745.353Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">SQL - MySQL</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path d="M504.4,115.83a5.72,5.72,0,0,0-.28-.68,8.52,8.52,0,0,0-.53-1.25,6,6,0,0,0-.54-.71,9.36,9.36,0,0,0-.72-.94c-.23-.22-.52-.4-.77-.6a8.84,8.84,0,0,0-.9-.68L404.4,55.55a8,8,0,0,0-8,0L300.12,111h0a8.07,8.07,0,0,0-.88.69,7.68,7.68,0,0,0-.78.6,8.23,8.23,0,0,0-.72.93c-.17.24-.39.45-.54.71a9.7,9.7,0,0,0-.52,1.25c-.08.23-.21.44-.28.68a8.08,8.08,0,0,0-.28,2.08V223.18l-80.22,46.19V63.44a7.8,7.8,0,0,0-.28-2.09c-.06-.24-.2-.45-.28-.68a8.35,8.35,0,0,0-.52-1.24c-.14-.26-.37-.47-.54-.72a9.36,9.36,0,0,0-.72-.94,9.46,9.46,0,0,0-.78-.6,9.8,9.8,0,0,0-.88-.68h0L115.61,1.07a8,8,0,0,0-8,0L11.34,56.49h0a6.52,6.52,0,0,0-.88.69,7.81,7.81,0,0,0-.79.6,8.15,8.15,0,0,0-.71.93c-.18.25-.4.46-.55.72a7.88,7.88,0,0,0-.51,1.24,6.46,6.46,0,0,0-.29.67,8.18,8.18,0,0,0-.28,2.1v329.7a8,8,0,0,0,4,6.95l192.5,110.84a8.83,8.83,0,0,0,1.33.54c.21.08.41.2.63.26a7.92,7.92,0,0,0,4.1,0c.2-.05.37-.16.55-.22a8.6,8.6,0,0,0,1.4-.58L404.4,400.09a8,8,0,0,0,4-6.95V287.88l92.24-53.11a8,8,0,0,0,4-7V117.92A8.63,8.63,0,0,0,504.4,115.83ZM111.6,17.28h0l80.19,46.15-80.2,46.18L31.41,63.44Zm88.25,60V278.6l-46.53,26.79-33.69,19.4V123.5l46.53-26.79Zm0,412.78L23.37,388.5V77.32L57.06,96.7l46.52,26.8V338.68a6.94,6.94,0,0,0,.12.9,8,8,0,0,0,.16,1.18h0a5.92,5.92,0,0,0,.38.9,6.38,6.38,0,0,0,.42,1v0a8.54,8.54,0,0,0,.6.78,7.62,7.62,0,0,0,.66.84l0,0c.23.22.52.38.77.58a8.93,8.93,0,0,0,.86.66l0,0,0,0,92.19,52.18Zm8-106.17-80.06-45.32,84.09-48.41,92.26-53.11,80.13,46.13-58.8,33.56Zm184.52,4.57L215.88,490.11V397.8L346.6,323.2l45.77-26.15Zm0-119.13L358.68,250l-46.53-26.79V131.79l33.69,19.4L392.37,178Zm8-105.28-80.2-46.17,80.2-46.16,80.18,46.15Zm8,105.28V178L455,151.19l33.68-19.4v91.39h0Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">PHP - Laravel</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 .333A9.911 9.911 0 0 0 6.866 19.65c.5.092.678-.215.678-.477 0-.237-.01-1.017-.014-1.845-2.757.6-3.338-1.169-3.338-1.169a2.627 2.627 0 0 0-1.1-1.451c-.9-.615.07-.6.07-.6a2.084 2.084 0 0 1 1.518 1.021 2.11 2.11 0 0 0 2.884.823c.044-.503.268-.973.63-1.325-2.2-.25-4.516-1.1-4.516-4.9A3.832 3.832 0 0 1 4.7 7.068a3.56 3.56 0 0 1 .095-2.623s.832-.266 2.726 1.016a9.409 9.409 0 0 1 4.962 0c1.89-1.282 2.717-1.016 2.717-1.016.366.83.402 1.768.1 2.623a3.827 3.827 0 0 1 1.02 2.659c0 3.807-2.319 4.644-4.525 4.889a2.366 2.366 0 0 1 .673 1.834c0 1.326-.012 2.394-.012 2.72 0 .263.18.572.681.475A9.911 9.911 0 0 0 10 .333Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Git - GitHub</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512">
                            <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                            <path d="M14 95.7924C14 42.8877 56.8878 0 109.793 0H274.161C327.066 0 369.954 42.8877 369.954 95.7924C369.954 129.292 352.758 158.776 326.711 175.897C352.758 193.019 369.954 222.502 369.954 256.002C369.954 308.907 327.066 351.795 274.161 351.795H272.081C247.279 351.795 224.678 342.369 207.666 326.904V415.167C207.666 468.777 163.657 512 110.309 512C57.5361 512 14 469.243 14 416.207C14 382.709 31.1945 353.227 57.2392 336.105C31.1945 318.983 14 289.5 14 256.002C14 222.502 31.196 193.019 57.2425 175.897C31.196 158.776 14 129.292 14 95.7924ZM176.288 191.587H109.793C74.2172 191.587 45.3778 220.427 45.3778 256.002C45.3778 291.44 73.9948 320.194 109.381 320.416C109.518 320.415 109.655 320.415 109.793 320.415H176.288V191.587ZM207.666 256.002C207.666 291.577 236.505 320.417 272.081 320.417H274.161C309.737 320.417 338.576 291.577 338.576 256.002C338.576 220.427 309.737 191.587 274.161 191.587H272.081C236.505 191.587 207.666 220.427 207.666 256.002ZM109.793 351.795C109.655 351.795 109.518 351.794 109.381 351.794C73.9948 352.015 45.3778 380.769 45.3778 416.207C45.3778 451.652 74.6025 480.622 110.309 480.622C146.591 480.622 176.288 451.186 176.288 415.167V351.795H109.793ZM109.793 31.3778C74.2172 31.3778 45.3778 60.2173 45.3778 95.7924C45.3778 131.368 74.2172 160.207 109.793 160.207H176.288V31.3778H109.793ZM207.666 160.207H274.161C309.737 160.207 338.576 131.368 338.576 95.7924C338.576 60.2173 309.737 31.3778 274.161 31.3778H207.666V160.207Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Figma</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 16a1 1 0 0 1-1-1v-1h2v1a1 1 0 0 1-1 1Z"/>
                            <path d="M17.989 6.124a6.5 6.5 0 0 0-12.495-2.1A5 5 0 0 0 6 14h4V8.414l-.293.293a1 1 0 0 1-1.414-1.414l2-2a1 1 0 0 1 1.414 0l2 2a1 1 0 1 1-1.414 1.414L12 8.414V14h5a4 4 0 0 0 .989-7.876Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Cloud - VPS</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2ZM7 2h4v3H7V2Zm5.7 8.289-3.975 3.857a1 1 0 0 1-1.393 0L5.3 12.182a1.002 1.002 0 1 1 1.4-1.436l1.328 1.289 3.28-3.181a1 1 0 1 1 1.392 1.435Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Gestión de Proyectos</h3>
                </div>
                <div class="text-center">
                    <div class="flex justify-center items-center mx-auto mb-4 w-10 h-10 rounded-full bg-delfinbeta-light lg:h-12 lg:w-12 dark:bg-delfinbeta-dark">
                        <svg class="w-5 h-5 text-delfinbeta-medium_light lg:w-6 lg:h-6 dark:text-delfinbeta-medium" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.5 0A3.987 3.987 0 0 0 11 2.1a4.977 4.977 0 0 1 3.9 5.858A3.989 3.989 0 0 0 14.5 0ZM9 13h2a4 4 0 0 1 4 4v2H5v-2a4 4 0 0 1 4-4Z"/>
                            <path d="M5 19h10v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2ZM5 7a5.008 5.008 0 0 1 4-4.9 3.988 3.988 0 1 0-3.9 5.859A4.974 4.974 0 0 1 5 7Zm5 3a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm5-1h-.424a5.016 5.016 0 0 1-1.942 2.232A6.007 6.007 0 0 1 17 17h2a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5ZM5.424 9H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h2a6.007 6.007 0 0 1 4.366-5.768A5.016 5.016 0 0 1 5.424 9Z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold dark:text-white">Liderazgo de Equipos</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog -->
    <section id="blog" class="bg-white dark:bg-delfinbeta-dark">
        <div class="py-12 lg:py-32 px-4 lg:px-6 mx-auto max-w-screen-xl">
            <div class="mx-auto max-w-screen-sm text-center lg:mb-16 mb-8">
                <h2 class="mb-4 text-3xl lg:text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">Blog</h2>
                <p class="font-light text-gray-500 sm:text-xl dark:text-gray-400">
                    Compartir contenido ayuda a nuestro propio aprendizaje, repasando conceptos y estructuras. Espero llegar a tenerlo como hábito.
                </p>
            </div> 
            <div class="grid gap-8 lg:grid-cols-2">
                <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-5 text-gray-500">
                        <span class="bg-delfinbeta-light text-delfinbeta-dark text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-delfinbeta-medium_light dark:text-delfinbeta-dark">
                            <svg class="mr-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path></svg>
                            Article
                        </span>
                        <span class="text-sm"></span>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        <a href="https://medium.com/@delfinbeta/frameworks-2cc6676c7249?source=friends_link&sk=6deb88e2e7877481dac20b341cd7f60d" target="_blank">
                            Frameworks
                        </a>
                    </h2>
                    <p class="mb-5 font-light text-gray-500 dark:text-gray-400">
                        Un Framework o Marco de Trabajo, es un conjunto de convenciones, estándares o paradigmas y buenas prácticas. Un marco de trabajo nos...
                    </p>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <span class="font-medium dark:text-gray-500">10 Febrero 2019</span>
                        </div>
                        <a href="https://medium.com/@delfinbeta/frameworks-2cc6676c7249?source=friends_link&sk=6deb88e2e7877481dac20b341cd7f60d" target="_blank" class="inline-flex items-center font-medium text-delfinbeta-medium dark:text-delfinbeta-medium hover:underline">
                            Leer más
                            <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </article> 
                <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-5 text-gray-500">
                        <span class="bg-delfinbeta-light text-delfinbeta-dark text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-delfinbeta-medium_light dark:text-delfinbeta-dark">
                            <svg class="mr-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"></path><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"></path></svg>
                            Article
                        </span>
                        <span class="text-sm"></span>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        <a href="https://medium.com/@delfinbeta/github-y-gitlab-8daaaba3399f?source=friends_link&sk=2efaaa943ac7951f53551b127a58727c" target="_blank">
                            GitHub y GitLab
                        </a>
                    </h2>
                    <p class="mb-5 font-light text-gray-500 dark:text-gray-400">
                        GitHub y GitLab son dos (2) plataformas de desarrollo colaborativo, que trabajan con el sistema de control de versiones Git.
                    </p>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <span class="font-medium dark:text-gray-500">12 Enero 2019</span>
                        </div>
                        <a href="https://medium.com/@delfinbeta/github-y-gitlab-8daaaba3399f?source=friends_link&sk=2efaaa943ac7951f53551b127a58727c" target="_blank" class="inline-flex items-center font-medium text-delfinbeta-medium dark:text-delfinbeta-medium hover:underline">
                            Leer más
                            <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>
                </article>                  
            </div>  
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="bg-white dark:bg-gray-900">
        <div class="py-12 lg:py-32 px-4">
            <div class="mx-auto max-w-screen-md">
                <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white">Contacto</h2>
                <p class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl">
                    ¿Necesitas crear tu web? ¿Quieres pasarte a la Nube? ¿Quieres aprender Programación Web (HTML - CSS - JS)? ¿Necesitas Asesoría en PHP - Laravel? Escríbeme
                </p>
            </div>
            <div class="mx-auto max-w-screen-xl grid gap-8 lg:grid-cols-3">
                <div class="font-light text-gray-500 dark:text-gray-400 sm:text-xl">
                    <div class="my-14">
                        <h3 class="mb-3 flex text-gray-800 dark:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 21">
                                <g stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <path d="M8 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                <path d="M13.8 12.938h-.01a7 7 0 1 0-11.465.144h-.016l.141.17c.1.128.2.252.3.372L8 20l5.13-6.248c.193-.209.373-.429.54-.66l.13-.154Z"/>
                                </g>
                            </svg>
                            <span class="ml-4">Ubicación</span>
                        </h3>
                        <p>Maracay - Venezuela</p>
                    </div>
                    <div class="my-14">
                        <h3 class="mb-3 flex text-gray-800 dark:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 20">
                                <path d="M12 0H2a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2ZM7.5 17.5h-1a1 1 0 0 1 0-2h1a1 1 0 0 1 0 2ZM12 13H2V4h10v9Z"/>
                            </svg>
                            <span class="ml-4">Teléfono - WhatsApp</span>
                        </h3>
                        <p>
                            <a href="https://wa.me/584243172126/?text=Hola%20vi%20tu%20website" target="_blank" class="hover:underline">
                                +58 (424) 317.2126
                            </a>
                        </p>
                    </div>
                    <div class="my-14">
                        <h3 class="mb-3 flex text-gray-800 dark:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 16">
                                <path d="m10.036 8.278 9.258-7.79A1.979 1.979 0 0 0 18 0H2A1.987 1.987 0 0 0 .641.541l9.395 7.737Z"/>
                                <path d="M11.241 9.817c-.36.275-.801.425-1.255.427-.428 0-.845-.138-1.187-.395L0 2.6V14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2.5l-8.759 7.317Z"/>
                            </svg> 
                            <span class="ml-4">Email</span>
                        </h3>
                        <p>
                            <a href="mailto:hola@delfinbeta.tech" target="_blank" class="hover:underline">
                                hola@delfinbeta.tech
                            </a>
                        </p>
                    </div>
                </div>
                <form action="#" class="space-y-8 lg:col-span-2">
                    <div class="grid gap-8 lg:grid-cols-2">
                        <div>
                            <x-label for="firstname" value="Nombre" />
                            <x-input type="text" name="firstname" id="firstname" :value="old('firstname')" placeholder="Juan" required />
                        </div>
                        <div>
                            <x-label for="lastname" value="Apellido" />
                            <x-input type="text" name="lastname" id="lastname" :value="old('lastname')" placeholder="Betancourt" required />
                        </div>
                        <div>
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input type="email" name="email" id="email" :value="old('email')" placeholder="name@company.com" required />
                        </div>
                        <div>
                            <x-label for="phone" value="Teléfono" />
                            <x-input type="text" name="phone" id="phone" :value="old('phone')" placeholder="+58 555 123 4678" />
                        </div>
                        <div class="lg:col-span-2">
                            <x-label for="message" value="Mensaje" />
                            <x-textarea name="message" id="message" placeholder="Deja tu mensaje..."></x-textarea>
                        </div>
                    </div>
                    <x-button>Enviar</x-button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="p-4 bg-white sm:p-6 dark:bg-delfinbeta-dark">
        <div class="mx-auto max-w-screen-xl">
            <div class="md:flex md:justify-between">
                <div class="md:w-1/2 mb-6 md:mb-0">
                    <div class="mx-auto max-w-screen-md">
                        <h3 class="mb-4 text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl dark:text-white">
                            Suscríbete a mi boletín
                        </h3>
                        <p class="mb-4 font-light text-gray-500 dark:text-gray-400">
                            Déjame tu email y compartiré contigo info sobre el mundo web.
                        </p>
                        <form action="#">
                            <div class="items-center mx-auto space-y-4 max-w-screen-sm sm:flex sm:space-y-0">
                                <div class="relative w-full">
                                    <label for="email" class="hidden mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email</label>
                                    <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                        </svg>
                                    </div>
                                    <input class="block p-3 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:rounded-none sm:rounded-l-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Escribe tu email" type="email" id="email" required />
                                </div>
                                <div>
                                    <button type="submit" class="py-3 px-5 w-full text-sm font-medium text-center text-white rounded-lg border cursor-pointer bg-delfinbeta-medium_dark border-delfinbeta-medium_dark sm:rounded-none sm:rounded-r-lg hover:bg-delfinbeta-medium focus:ring-4 focus:ring-delfinbeta-light dark:bg-delfinbeta-medium_dark dark:hover:bg-delfinbeta-medium dark:focus:ring-delfinbeta-medium">Subscríbete</button>
                                </div>
                            </div>
                            <!-- <div class="mx-auto max-w-screen-sm text-sm text-left text-gray-500 newsletter-form-footer dark:text-gray-300">We care about the protection of your data. <a href="#" class="font-medium text-primary-600 dark:text-primary-500 hover:underline">Read our Privacy Policy</a>.</div> -->
                        </form>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="my-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Trabajo</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li class="mb-4">
                                <a href="#" class="hover:underline">Portafolio</a>
                            </li>
                            <li>
                                <a href="https://github.com/delfinbeta/" target="_blank" class="hover:underline">Github</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="my-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Contenido</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li class="mb-4">
                                <a href="#" class="hover:underline ">Blog</a>
                            </li>
                            <li>
                                <a href="https://medium.com/@delfinbeta/" target="_blank" class="hover:underline">Medium</a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="my-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Comunidad</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li class="mb-4">
                                <a href="https://hf.cx" target="_blank" class="hover:underline">Hackers / Founders</a>
                            </li>
                            <li>
                                <a href="https://hfmaracay.com" target="_blank" class="hover:underline">H/F Maracay</a>
                            </li>
                        </ul>
                    </div>
                    <!-- <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                        <ul class="text-gray-600 dark:text-gray-400">
                            <li class="mb-4">
                                <a href="#" class="hover:underline">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="#" class="hover:underline">Terms &amp; Conditions</a>
                            </li>
                        </ul>
                    </div> -->
                </div>
            </div>
            <hr class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
                    &copy; {{ date('Y') }} Derechos Reservados
                </span>
                <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
                    <a href="https://twitter.com/delfinbeta" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/delfinbeta" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/delfinbeta/" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="https://github.com/delfinbeta/" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/in/delfinbeta/" target="_blank" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.979 5v1.586a3.5 3.5 0 0 1 3.082-1.574C14.3 5.012 15 7.03 15 9.655V15h-3v-4.738c0-1.13-.229-2.584-1.995-2.584-1.713 0-2.005 1.23-2.005 2.5V15H5.009V5h2.97ZM3 2.487a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" clip-rule="evenodd"/>
                            <path d="M3 5.012H0V15h3V5.012Z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @stack('modals')

    @livewireScripts

    <script>
        (function () {
            'use strict';

            /*----------------------  Scroll Page  -----------------------*/
            window.addEventListener('scroll', function(e) {
                let menu = document.getElementById('menu');
                let logo = document.getElementById('logo');
                // let btnScroll = document.getElementById('btn-scroll');

                if (window.scrollY > 20) {
                    menu.classList.add("bg-delfinbeta-medium");
                    menu.classList.add("dark:bg-delfinbeta-medium");
                    menu.classList.remove("dark:bg-delfinbeta-dark");
                    logo.src = '{{ asset("img/DelfinBeta.png") }}';
                    // btnScroll.classList.remove("hidden");
                } else {
                    menu.classList.remove("bg-delfinbeta-medium");
                    menu.classList.remove("dark:bg-delfinbeta-medium");
                    menu.classList.add("dark:bg-delfinbeta-dark");
                    logo.src = '{{ asset("img/DelfinBeta_White.png") }}';
                    // btnScroll.classList.add("hidden");
                }
            });
            /*------------------------------------------------------------*/
        })();
    </script>
</body>
</html>
