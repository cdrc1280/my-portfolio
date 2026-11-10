<?php

declare(strict_types=1);

namespace App\Domain\Contact\Models;

use App\Domain\Contact\Policies\ContactMessagePolicy;
use App\Domain\Contact\QueryBuilders\ContactMessageQueryBuilder;
use Carbon\CarbonImmutable;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $subject
 * @property string $message
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property CarbonImmutable|null $read_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static ContactMessageQueryBuilder<static> query()
 */
#[Fillable(['name', 'email', 'subject', 'message', 'ip_address', 'user_agent', 'read_at'])]
#[UseEloquentBuilder(ContactMessageQueryBuilder::class)]
#[UseFactory(ContactMessageFactory::class)]
#[UsePolicy(ContactMessagePolicy::class)]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
