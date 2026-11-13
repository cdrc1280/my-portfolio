<?php

declare(strict_types=1);

namespace App\Domains\Contact\Controllers\Public;

use App\Domains\Contact\Actions\SubmitContactMessageAction;
use App\Domains\Contact\Data\ContactMessageData;
use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\Currency;
use App\Domains\Contact\Enums\ProjectTimeline;
use App\Domains\Contact\Enums\ProjectType;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('public/contact', [
            'projectTypes' => ProjectType::options(),
            'currencies' => Currency::options(),
            // Bracket labels per currency, so the form can switch without a round trip.
            'budgets' => collect(Currency::cases())
                ->mapWithKeys(fn (Currency $currency): array => [$currency->value => BudgetRange::optionsIn($currency)])
                ->all(),
            'timelines' => ProjectTimeline::options(),
        ]);
    }

    public function store(ContactMessageData $data, Request $request, SubmitContactMessageAction $action): RedirectResponse
    {
        // Bots get the same response as people, so the trap isn't revealed.
        if ($data->isSpam()) {
            Log::info('Contact form honeypot triggered.', ['ip' => $request->ip()]);
        } else {
            $action->execute($data, $request->ip());
        }

        $this->toast('Thanks, your message is in. A confirmation is on its way to your inbox.');

        return to_route('contact.show');
    }
}
