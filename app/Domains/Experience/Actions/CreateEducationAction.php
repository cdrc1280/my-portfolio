<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Data\EducationData;
use App\Domains\Experience\Models\Education;
use Illuminate\Support\Facades\DB;

final class CreateEducationAction
{
    public function execute(EducationData $data): Education
    {
        return DB::transaction(fn (): Education => Education::query()->create(self::columns($data)));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(EducationData $data): array
    {
        return [
            'institution' => $data->institution,
            'qualification' => $data->qualification,
            'field_of_study' => $data->fieldOfStudy,
            'started_on' => $data->startedOn,
            'ended_on' => $data->endedOn,
            'notes' => $data->notes,
        ];
    }
}
