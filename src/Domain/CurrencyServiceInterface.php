<?php declare(strict_types=1);

namespace App\Domain;

use Money\Currency;

interface CurrencyServiceInterface
{
    public function getCurrency(): Currency;
}
