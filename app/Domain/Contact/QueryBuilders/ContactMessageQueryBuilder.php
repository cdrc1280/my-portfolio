<?php

declare(strict_types=1);

namespace App\Domain\Contact\QueryBuilders;

use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of ContactMessage
 *
 * @extends Builder<TModel>
 */
class ContactMessageQueryBuilder extends Builder
{
    /**
     * @return $this
     */
    public function unread(): static
    {
        return $this->whereNull('read_at');
    }

    /**
     * @return $this
     */
    public function read(): static
    {
        return $this->whereNotNull('read_at');
    }
}
