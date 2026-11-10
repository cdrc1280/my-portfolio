<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the admin account and the sample portfolio content.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => config('portfolio.admin.email')],
            [
                'name' => config('portfolio.admin.name'),
                'password' => config('portfolio.admin.password'),
                'email_verified_at' => now(),
            ],
        )->forceFill(['is_admin' => true])->save();

        $this->call(PortfolioSeeder::class);
    }
}
