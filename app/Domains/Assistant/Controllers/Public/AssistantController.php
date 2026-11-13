<?php

declare(strict_types=1);

namespace App\Domains\Assistant\Controllers\Public;

use App\Domains\Assistant\Data\AssistantQuestionData;
use App\Domains\Assistant\Queries\AnswerQuestionQuery;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * The public "Ask about me" assistant. Answers come from the site's own content; see AnswerQuestionQuery.
 */
class AssistantController extends Controller
{
    public function __invoke(AssistantQuestionData $data, GetProfileAction $getProfile, AnswerQuestionQuery $answer): JsonResponse
    {
        abort_unless($getProfile->snapshot()->assistantEnabled, 404);

        return response()->json($answer->execute($data->question)->toArray());
    }
}
