<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\GitHub\Actions\GetGitHubActivityAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('github:refresh')]
#[Description('Refresh the cached GitHub activity shown on the home page')]
class RefreshGitHubActivity extends Command
{
    public function handle(GetGitHubActivityAction $action): int
    {
        $activity = $action->refresh();

        if ($activity === null) {
            $this->warn('GitHub activity unavailable (no username configured, or the API failed).');

            return self::FAILURE;
        }

        $this->info("Cached activity for {$activity->username}: {$activity->publicRepos} public repos.");

        return self::SUCCESS;
    }
}
