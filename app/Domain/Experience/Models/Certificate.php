<?php

declare(strict_types=1);

namespace App\Domain\Experience\Models;

use App\Domain\Experience\Policies\ExperiencePolicy;
use App\Domain\Shared\Concerns\HasPosition;
use Carbon\CarbonImmutable;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $issuer
 * @property CarbonImmutable $issued_on
 * @property string|null $credential_url
 * @property int $position
 */
#[Fillable(['name', 'issuer', 'issued_on', 'credential_url', 'position'])]
#[UseFactory(CertificateFactory::class)]
#[UsePolicy(ExperiencePolicy::class)]
class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use HasFactory, HasPosition;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_on' => 'immutable_date',
            'position' => 'integer',
        ];
    }
}
