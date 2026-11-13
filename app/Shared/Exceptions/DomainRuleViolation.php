<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use RuntimeException;

/**
 * A business rule that blocks an operation (as opposed to a field validation error). Thrown from an
 * Action; bootstrap/app.php turns it into an error toast and sends the user back.
 */
final class DomainRuleViolation extends RuntimeException {}
