<?php

declare(strict_types=1);

namespace App\Domains\Profile\Controllers\Admin;

use App\Domains\Profile\Actions\UpdateAvailabilityAction;
use App\Domains\Profile\Data\AvailabilityData;
use App\Domains\Profile\Models\Profile;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AvailabilityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('can:update,'.Profile::class)];
    }

    public function __invoke(AvailabilityData $data, UpdateAvailabilityAction $action): RedirectResponse
    {
        $profile = $action->execute($data->availabilityStatus, $data->availability);

        $this->toast('Availability set to “'.$profile->availability_status->label().'”.');

        return back();
    }
}
