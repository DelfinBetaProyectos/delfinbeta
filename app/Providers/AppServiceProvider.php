<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
    }

    /**
     * Limit the contact form per visitor, sending them back to the form with a message instead of a 429 page.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('contact', fn (Request $request): array => collect([
            'minute' => Limit::perMinute(3),
            'day' => Limit::perDay(10),
        ])->map(fn (Limit $limit, string $window): Limit => $limit
            ->by("{$window}:{$request->ip()}")
            ->response(fn (): RedirectResponse => redirect()
                ->to(url()->previous().'#'.__('nav.sections.contact.anchor'))
                ->withInput()
                ->withErrors(['message' => __('home.contact.form.throttled')])))
            ->values()
            ->all());
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
