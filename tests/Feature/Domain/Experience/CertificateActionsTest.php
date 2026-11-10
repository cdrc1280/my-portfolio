<?php

use App\Domain\Experience\Actions\CreateCertificateAction;
use App\Domain\Experience\Actions\DeleteCertificateAction;
use App\Domain\Experience\Actions\UpdateCertificateAction;
use App\Domain\Experience\DataTransferObjects\CertificateData;

test('creates, updates and deletes a certificate', function () {
    $certificate = app(CreateCertificateAction::class)->execute(CertificateData::fromValidated([
        'name' => 'Cybersecurity Basics',
        'issuer' => 'DICT',
        'issued_on' => '2025-06',
        'credential_url' => '',
    ]));

    expect($certificate)->credential_url->toBeNull()->issued_on->format('Y-m-d')->toBe('2025-06-01');

    app(UpdateCertificateAction::class)->execute($certificate, CertificateData::fromValidated([
        'name' => 'Cybersecurity Essentials',
        'issuer' => 'DICT',
        'issued_on' => '2025-07',
    ]));
    expect($certificate->fresh()->name)->toBe('Cybersecurity Essentials');

    app(DeleteCertificateAction::class)->execute($certificate);
    $this->assertModelMissing($certificate);
});
