<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Contact\Actions\SubmitContactMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactMessageRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('public/contact');
    }

    public function store(StoreContactMessageRequest $request, SubmitContactMessageAction $action): RedirectResponse
    {
        // Bots get the same response as people, so the trap isn't revealed.
        if ($request->isSpam()) {
            Log::info('Contact form honeypot triggered.', ['ip' => $request->ip()]);
        } else {
            $action->execute($request->toData());
        }

        $this->toast("Thanks for reaching out — I'll get back to you soon.");

        return to_route('contact.show');
    }
}
