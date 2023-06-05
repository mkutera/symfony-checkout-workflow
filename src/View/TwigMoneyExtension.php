<?php declare(strict_types=1);

namespace App\View;

use Locale;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigMoneyExtension extends AbstractExtension
{
    private string $locale;

    private ISOCurrencies $currencies;

    public function __construct()
    {
        $this->locale = Locale::getDefault();
        $this->currencies = new ISOCurrencies();
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('money_format', [$this, 'formatMoney']),
        ];
    }

    public function formatMoney(?Money $money = null): string
    {
        if ($money === null) {
            return '';
        }

        $numberFormatter = new NumberFormatter($this->locale, NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $this->currencies);

        return $moneyFormatter->format($money);
    }
}
