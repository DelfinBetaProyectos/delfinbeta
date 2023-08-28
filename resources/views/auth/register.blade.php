<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
            <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                Regístrate en DelfinBeta
            </h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-4 md:space-y-6">
                @csrf

                <div>
                    <x-label for="name" value="{{ __('Name') }}" />
                    <x-input type="text" name="name" id="name" :value="old('name')" placeholder="Juan Betancourt" required autofocus autocomplete="name" />
                </div>

                <div>
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input type="email" name="email" id="email" :value="old('email')" placeholder="name@company.com" required autocomplete="username" />
                </div>

                <div>
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="new-password" />
                </div>

                <div>
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                    <x-input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required autocomplete="new-password" />
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div>
                        <x-label for="terms">
                            <div class="flex items-center">
                                <x-checkbox name="terms" id="terms" required />

                                <div class="ml-2">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                @endif

                <x-button>
                    {{ __('Register') }}
                </x-button>
                
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:underline dark:text-primary-500">Login here</a>
                </p>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
