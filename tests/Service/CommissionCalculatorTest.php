<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Tests\Service;

use Armezit\CommissionCalculator\Models\Operation;
use Armezit\CommissionCalculator\Service\CommissionCalculator;
use Armezit\CommissionCalculator\Service\ExchangeRate;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @var CommissionCalculator
     */
    private $commissionCalculator;

    public function setUp()
    {
        $config = require __DIR__ . '/../../config/app.php';
        $exchangeRate = new ExchangeRate([
            'USD' => '1.1497',
            'JPY' => '129.53',
        ]);
        $this->commissionCalculator = new CommissionCalculator($config);
    }

    /**
     * @param string $date
     * @param string $userId
     * @param string $userType
     * @param string $type
     * @param string $amount
     * @param string $currency
     * @param string $expectation
     *
     * @dataProvider dataProviderForAddTesting
     */
    public function testAdd(
        string $date,
        string $userId,
        string $userType,
        string $type,
        string $amount,
        string $currency,
        string $expectation
    )
    {
        $operation = new Operation(
            (new \DateTime())->setTimestamp(strtotime($date)),
            $userId,
            $userType,
            $type,
            $amount,
            $currency
        );

        $commission = $this->commissionCalculator->execute($operation);
        $this->assertEquals($expectation, $commission);
    }

    public function dataProviderForAddTesting(): array
    {
        return [
            '2014-12-31,4,private,withdraw,1200.00,EUR' => ['2014-12-31', '4', 'private', 'withdraw', '1200.00', 'EUR', '0.60'],
            '2015-01-01,4,private,withdraw,1000.00,EUR' => ['2015-01-01', '4', 'private', 'withdraw', '1000.00', 'EUR', '3.60'],
            '2016-01-05,4,private,withdraw,1000.00,EUR' => ['2016-01-05', '4', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            '2016-01-05,1,private,deposit,200.00,EUR' => ['2016-01-05', '1', 'private', 'deposit', '200.00', 'EUR', '0.06'],
            '2016-01-06,2,business,withdraw,300.00,EUR' => ['2016-01-06', '2', 'business', 'withdraw', '300.00', 'EUR', '1.50'],
            '2016-01-06,1,private,withdraw,30000,JPY' => ['2016-01-06', '1', 'private', 'withdraw', '30000', 'JPY', '0'],
            '2016-01-07,1,private,withdraw,1000.00,EUR' => ['2016-01-07', '1', 'private', 'withdraw', '1000.00', 'EUR', '0.70'],
            '2016-01-07,1,private,withdraw,100.00,USD' => ['2016-01-07', '1', 'private', 'withdraw', '100.00', 'USD', '0.30'],
            '2016-01-10,1,private,withdraw,100.00,EUR' => ['2016-01-10', '1', 'private', 'withdraw', '100.00', 'EUR', '0.30'],
            '2016-01-10,2,business,deposit,10000.00,EUR' => ['2016-01-10', '2', 'business', 'deposit', '10000.00', 'EUR', '3.00'],
            '2016-01-10,3,private,withdraw,1000.00,EUR' => ['2016-01-10', '3', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            '2016-02-15,1,private,withdraw,300.00,EUR' => ['2016-02-15', '1', 'private', 'withdraw', '300.00', 'EUR', '0.00'],
            '2016-02-19,5,private,withdraw,3000000,JPY' => ['2016-02-19', '5', 'private', 'withdraw', '3000000', 'JPY', '8612'],
        ];
    }
}
