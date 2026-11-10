<?php

declare(strict_types=1);

namespace App\Domain\Skills\Policies;

use App\Domain\Shared\Policies\AdminPolicy;

/**
 * Covers both skills and skill categories; they are managed together.
 */
final class SkillPolicy extends AdminPolicy {}
