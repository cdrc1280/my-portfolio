<?php

namespace App\Providers;

use App\Domains\Bookings\Support\CalendlyClient;
use App\Domains\GitHub\Support\GitHubClient;
use App\Models\User;
use App\Shared\Storage\DatabaseFilesystemAdapter;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GitHubClient::class, fn (): GitHubClient => GitHubClient::fromConfig());
        $this->app->bind(CalendlyClient::class, fn (): CalendlyClient => CalendlyClient::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthorization();
        $this->configureDatabaseDisk();
        $this->configureRateLimiting();
    }

    /**
     * Public contact form: a few messages per hour per IP.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('contact', fn (Request $request): Limit => Limit::perHour((int) config('portfolio.contact.per_hour'))
            ->by('contact:'.$request->ip()));

        RateLimiter::for('assistant', fn (Request $request): Limit => Limit::perMinute(20)
            ->by('assistant:'.$request->ip()));
    }

    /**
     * Admin-only CMS: every /admin route requires this gate.
     */
    protected function configureAuthorization(): void
    {
        Gate::define('access-admin', fn (User $user): bool => $user->is_admin);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // Resources are passed to Inertia as plain objects, not {data: ...}.
        JsonResource::withoutWrapping();

        // Surface lazy loading / silently discarded attributes outside production.
        Model::shouldBeStrict(! app()->isProduction());

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

    /**
     * The `database` filesystem driver: uploads kept in the stored_files table (used on Vercel,
     * which has no persistent disk). See App\Shared\Storage\DatabaseFilesystemAdapter.
     */
    private function configureDatabaseDisk(): void
    {
        Storage::extend('database', function ($app, array $config): FilesystemAdapter {
            $adapter = new DatabaseFilesystemAdapter(
                DB::connection(is_string($config['connection'] ?? null) ? $config['connection'] : null),
                (string) ($config['url'] ?? ''),
            );

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });
    }
}
