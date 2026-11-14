<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

/**
 * Brings the database up to date for a new release, safely from many instances at once:
 * migrates under a database-level lock, then seeds the admin account and real content only if
 * the site has never been set up, then copies ADMIN_EMAIL and ADMIN_PASSWORD onto the admin.
 * Vercel runs it when a container starts (docker/vercel/start.sh), so a deploy needs no manual
 * database step.
 *
 * If migrating fails before the site was ever set up (no users yet), the database can only hold a
 * half-finished first setup, for example from an instance stopped mid-migration. It is wiped and
 * built again. Once a user exists, a failure is reported and nothing is ever wiped.
 */
class ReleaseCommand extends Command
{
    protected $signature = 'app:release';

    protected $description = 'Migrate (under a lock) and seed the site on its very first release';

    /** Arbitrary, stable key for the Postgres advisory lock. */
    private const LOCK_KEY = 4_180_201;

    public function handle(): int
    {
        $postgres = DB::connection()->getDriverName() === 'pgsql';

        if ($postgres) {
            // Waits while another starting instance holds the lock, so migrations never race.
            DB::select('select pg_advisory_lock(?)', [self::LOCK_KEY]);
        }

        try {
            if (! $this->migrate()) {
                if ($this->siteIsSetUp()) {
                    $this->error('Migrating failed on a site that is already set up; nothing was changed.');

                    return self::FAILURE;
                }

                $this->warn('Migrating failed before the site was ever set up: rebuilding the database.');
                $this->call('db:wipe', ['--force' => true]);

                if (! $this->migrate()) {
                    return self::FAILURE;
                }
            }

            if (! $this->siteIsSetUp()) {
                $this->info('First release: seeding the admin account and portfolio content.');
                $this->call('db:seed', ['--force' => true]);
            }

            $this->syncAdminLogin();
        } finally {
            if ($postgres) {
                DB::select('select pg_advisory_unlock(?)', [self::LOCK_KEY]);
            }
        }

        return self::SUCCESS;
    }

    private function migrate(): bool
    {
        try {
            return $this->call('migrate', ['--force' => true]) === self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return false;
        }
    }

    /**
     * Copies ADMIN_EMAIL and ADMIN_PASSWORD onto the existing admin, so changing them in the host's
     * environment and redeploying changes the login. Skipped unless both are set.
     */
    private function syncAdminLogin(): void
    {
        if (! config('portfolio.admin.sync')) {
            return;
        }

        $admin = User::query()->where('is_admin', true)->oldest('id')->first();

        if ($admin === null) {
            return;
        }

        $email = (string) config('portfolio.admin.email');
        $password = (string) config('portfolio.admin.password');

        if ($admin->email !== $email) {
            $admin->email = $email;
        }

        if (! Hash::check($password, $admin->password)) {
            $admin->password = $password; // Hashed by the model's cast.
        }

        if ($admin->isDirty()) {
            $admin->save();
            $this->info('Updated the admin login from ADMIN_EMAIL and ADMIN_PASSWORD.');
        }
    }

    private function siteIsSetUp(): bool
    {
        try {
            return User::query()->exists();
        } catch (Throwable) {
            return false; // No users table yet.
        }
    }
}
