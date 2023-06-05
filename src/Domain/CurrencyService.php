<?php declare(strict_types=1);

namespace App\Domain;

use Money\Currency;

class CurrencyService implements CurrencyServiceInterface
{
    public function __construct(private readonly string $currencyCode)
    {
    }

    public function getCurrency(): Currency
    {
        return new Currency($this->currencyCode);
    }
}
