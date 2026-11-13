<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Data\ExperienceData;
use App\Domains\Experience\Models\Experience;
use Illuminate\Support\Facades\DB;

final class CreateExperienceAction
{
    public function execute(ExperienceData $data): Experience
    {
        return DB::transaction(fn (): Experience => Experience::query()->create(self::columns($data)));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(ExperienceData $data): array
    {
        return [
            'company' => $data->company,
            'company_url' => $data->companyUrl,
            'role' => $data->role,
            'location' => $data->location,
            'is_confidential' => $data->isConfidential,
            'is_remote' => $data->isRemote,
            'employment_type' => $data->employmentType,
            'started_on' => $data->startedOn,
            'ended_on' => $data->endedOn,
            'summary' => $data->summary,
            'highlights' => $data->highlights,
            'technologies' => $data->technologies,
        ];
    }
}
