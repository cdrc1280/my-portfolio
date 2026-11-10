<?php

declare(strict_types=1);

namespace App\Domain\Contact\Policies;

use App\Domain\Shared\Policies\AdminPolicy;

/**
 * Inbox management is admin-only. Submitting a message is public and is
 * guarded by rate limiting and a honeypot rather than a policy.
 */
final class ContactMessagePolicy extends AdminPolicy {}
