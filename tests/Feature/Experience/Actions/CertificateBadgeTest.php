<?php

use App\Domains\Experience\Actions\CreateCertificateAction;
use App\Domains\Experience\Actions\DeleteCertificateAction;
use App\Domains\Experience\Actions\UpdateCertificateAction;
use App\Domains\Experience\Data\CertificateData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('certificates store, replace and delete their badge', function () {
    Storage::fake('public');

    $certificate = app(CreateCertificateAction::class)->execute(CertificateData::from([
        'name' => 'AWS', 'issuer' => 'Amazon', 'issued_on' => '2025-06', 'badge' => UploadedFile::fake()->image('a.png'),
    ]));
    $first = $certificate->badge_path;
    Storage::disk('public')->assertExists($first);

    app(UpdateCertificateAction::class)->execute($certificate, CertificateData::from([
        'name' => 'AWS', 'issuer' => 'Amazon', 'issued_on' => '2025-06', 'badge' => UploadedFile::fake()->image('b.png'),
    ]));
    Storage::disk('public')->assertMissing($first);

    $second = $certificate->fresh()->badge_path;
    app(DeleteCertificateAction::class)->execute($certificate->fresh());
    Storage::disk('public')->assertMissing($second);
});
