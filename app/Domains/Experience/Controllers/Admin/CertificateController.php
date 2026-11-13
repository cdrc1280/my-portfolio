<?php

declare(strict_types=1);

namespace App\Domains\Experience\Controllers\Admin;

use App\Domains\Experience\Actions\CreateCertificateAction;
use App\Domains\Experience\Actions\DeleteCertificateAction;
use App\Domains\Experience\Actions\ReorderCareerHistoryAction;
use App\Domains\Experience\Actions\UpdateCertificateAction;
use App\Domains\Experience\Data\CertificateData;
use App\Domains\Experience\Models\Certificate;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CertificateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:create,'.Certificate::class, only: ['store']),
            new Middleware('can:update,certificate', only: ['update']),
            new Middleware('can:reorder,'.Certificate::class, only: ['reorder']),
            new Middleware('can:delete,certificate', only: ['destroy']),
        ];
    }

    public function store(CertificateData $data, CreateCertificateAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Certificate added.');

        return to_route('admin.experience.index');
    }

    public function update(CertificateData $data, Certificate $certificate, UpdateCertificateAction $action): RedirectResponse
    {
        $action->execute($certificate, $data);

        $this->toast('Certificate updated.');

        return to_route('admin.experience.index');
    }

    public function destroy(Certificate $certificate, DeleteCertificateAction $action): RedirectResponse
    {
        $action->execute($certificate);

        $this->toast('Certificate deleted.');

        return to_route('admin.experience.index');
    }

    public function reorder(ReorderData $data, ReorderCareerHistoryAction $action): RedirectResponse
    {
        $action->execute(Certificate::class, $data->ids);

        return to_route('admin.experience.index');
    }
}
