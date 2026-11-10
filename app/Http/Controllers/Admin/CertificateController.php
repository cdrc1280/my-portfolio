<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Experience\Actions\CreateCertificateAction;
use App\Domain\Experience\Actions\DeleteCertificateAction;
use App\Domain\Experience\Actions\ReorderCareerHistoryAction;
use App\Domain\Experience\Actions\UpdateCertificateAction;
use App\Domain\Experience\Models\Certificate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Experience\StoreCertificateRequest;
use App\Http\Requests\Admin\Experience\UpdateCertificateRequest;
use App\Http\Requests\Admin\ReorderRequest;
use Illuminate\Http\RedirectResponse;

class CertificateController extends Controller
{
    public function store(StoreCertificateRequest $request, CreateCertificateAction $action): RedirectResponse
    {
        $action->execute($request->toData());

        $this->toast('Certificate added.');

        return to_route('admin.experience.index');
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate, UpdateCertificateAction $action): RedirectResponse
    {
        $action->execute($certificate, $request->toData());

        $this->toast('Certificate updated.');

        return to_route('admin.experience.index');
    }

    public function destroy(Certificate $certificate, DeleteCertificateAction $action): RedirectResponse
    {
        $this->authorize('delete', $certificate);

        $action->execute($certificate);

        $this->toast('Certificate deleted.');

        return to_route('admin.experience.index');
    }

    public function reorder(ReorderRequest $request, ReorderCareerHistoryAction $action): RedirectResponse
    {
        $this->authorize('reorder', Certificate::class);

        $action->execute(Certificate::class, $request->ids());

        return to_route('admin.experience.index');
    }
}
