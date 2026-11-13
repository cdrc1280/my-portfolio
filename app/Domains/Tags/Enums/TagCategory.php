<?php

declare(strict_types=1);

namespace App\Domains\Tags\Enums;

use App\Shared\Concerns\HasOptions;

/**
 * Groups tags in the public skills section. Tags without a category are project badges only.
 */
enum TagCategory: string
{
    use HasOptions;

    case Backend = 'backend';
    case Frontend = 'frontend';
    case Database = 'database';
    case DevopsTools = 'devops_tools';
    case Testing = 'testing';
    case AiTools = 'ai_tools';

    public function label(): string
    {
        return match ($this) {
            self::Backend => 'Backend',
            self::Frontend => 'Frontend',
            self::Database => 'Database',
            self::DevopsTools => 'DevOps & tools',
            self::Testing => 'Testing',
            self::AiTools => 'AI tools',
        };
    }
}
