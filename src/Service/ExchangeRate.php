<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Service;

final class ExchangeRate
{
    /**
     * @var array
     */
    private static $rates;

    public function __construct(array $rates = [])
    {
        self::$rates = $rates;
    }

    public function fetch(): array
    {
        $url = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($resp, true);
        self::$rates = $response['rates'] ?? [];

        return $response;
    }

    /**
     * get all currency rates.
     */
    public static function getRates(): array
    {
        return self::$rates;
    }

    /**
     * get currency exchange rate.
     */
    public static function getRate(string $currency): string
    {
        return (string) self::$rates[$currency];
    }
}
