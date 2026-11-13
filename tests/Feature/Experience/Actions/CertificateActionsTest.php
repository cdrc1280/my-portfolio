<?php

use App\Domains\Experience\Actions\CreateCertificateAction;
use App\Domains\Experience\Actions\DeleteCertificateAction;
use App\Domains\Experience\Actions\UpdateCertificateAction;
use App\Domains\Experience\Data\CertificateData;

test('creates, updates and deletes a certificate', function () {
    $certificate = app(CreateCertificateAction::class)->execute(CertificateData::from([
        'name' => 'Cybersecurity Basics',
        'issuer' => 'DICT',
        'issued_on' => '2025-06',
        'credential_url' => '',
    ]));

    expect($certificate)->credential_url->toBeNull()->issued_on->format('Y-m-d')->toBe('2025-06-01');

    app(UpdateCertificateAction::class)->execute($certificate, CertificateData::from([
        'name' => 'Cybersecurity Essentials',
        'issuer' => 'DICT',
        'issued_on' => '2025-07',
    ]));
    expect($certificate->fresh()->name)->toBe('Cybersecurity Essentials');

    app(DeleteCertificateAction::class)->execute($certificate);
    $this->assertModelMissing($certificate);
});
