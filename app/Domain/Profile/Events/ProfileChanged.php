<?php

declare(strict_types=1);

namespace App\Domain\Profile\Events;

use Illuminate\Foundation\Events\Dispatchable;

/**
 * The public profile or its social links changed.
 */
final class ProfileChanged
{
    use Dispatchable;
}
