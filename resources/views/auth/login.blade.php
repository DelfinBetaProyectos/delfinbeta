<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                Iniciar sesión
            </h1>

            <form method="POST" action="{{ route('login') }}" class="space-y-4 md:space-y-6">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input type="email" name="email" id="email" :value="old('email')" placeholder="name@company.com" required autofocus />
                </div>

                <div>
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input type="password" name="password" id="password" placeholder="••••••••" required />
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="remember" aria-describedby="remember" type="checkbox" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="remember" class="text-gray-500 dark:text-gray-300">{{ __('Remember me') }}</label>
                        </div>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="#" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <x-button>
                    {{ __('Log in') }}
                </x-button>

                @if (Route::has('register'))
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        Don’t have an account yet? 
                        <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Sign up</a>
                    </p>
                @endif
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
