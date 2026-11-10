<?php

declare(strict_types=1);

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\SocialLink;
use App\Domain\Shared\Actions\ReorderAction;

final class ReorderSocialLinksAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(SocialLink::class, $orderedIds);

        ProfileChanged::dispatch();
    }
}
