<?php

use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\Currency;

test('brackets are labelled in the chosen currency', function () {
    expect(BudgetRange::Under1k->labelIn(Currency::Php))->toBe('Under ₱50,000')
        ->and(BudgetRange::From1kTo3k->labelIn(Currency::Eur))->toBe('€1,000 – €3,000')
        ->and(BudgetRange::Over8k->labelIn(Currency::Aud))->toBe('A$12,000+')
        ->and(BudgetRange::NotSure->labelIn(Currency::Gbp))->toBe('Not sure yet');
});

test('the plain label is in US dollars', function () {
    expect(BudgetRange::From3kTo8k->label())->toBe('$3,000 – $8,000');
});

test('every currency has increasing thresholds', function (Currency $currency) {
    [$small, $medium, $large] = $currency->thresholds();

    expect($small)->toBeLessThan($medium)->and($medium)->toBeLessThan($large);
})->with(Currency::cases());
