<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Brings the database up to date for a new release, safely from many instances at once:
 * migrates under a database-level lock, then seeds the admin account and real content only if
 * the site has never been set up. Vercel runs it when a container starts (docker/vercel/start.sh),
 * so a deploy needs no manual database step.
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
            $this->call('migrate', ['--force' => true]);

            if (! User::query()->exists()) {
                $this->info('First release: seeding the admin account and portfolio content.');
                $this->call('db:seed', ['--force' => true]);
            }
        } finally {
            if ($postgres) {
                DB::select('select pg_advisory_unlock(?)', [self::LOCK_KEY]);
            }
        }

        return self::SUCCESS;
    }
}
