<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Service;

use Armezit\CommissionCalculator\Models\Operation;
use Armezit\CommissionCalculator\Rules\Engine;

class CommissionCalculator
{
    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var Math
     */
    private $math;

    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    public static $operations = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->engine = new Engine($this->config);
        $this->math = new Math(2);
    }

    public function execute(Operation $operation): string
    {
        // calculate base amount (i.e. amount value in base currency)
        if ($operation->currency !== $this->config['base_currency']) {
            $operation->baseAmount = $this->math->div($operation->amount, ExchangeRate::getRate($operation->currency));
        } else {
            $operation->baseAmount = $operation->amount;
        }

        // partition operations data
        $weekId = $this->weekId($operation->date);
        $this->pushOperation($weekId, $operation);

        // execute rule engine
        $commission = $this->engine->execute([
            'op' => $operation,
            'ops' => self::$operations,
            'week' => $weekId,
        ]);

        // convert commission amount to original currency
        if ($operation->currency !== $this->config['base_currency']) {
            $rate = ExchangeRate::getRate($operation->currency);
            $commission = $this->math->setScale(10)->mul($commission, $rate);
        }

        return $this->formatValue($commission, $this->config['currencies'][$operation->currency]['decimals']);
    }

    /**
     * @param $value
     */
    private function formatValue($value, int $decimals = 2): string
    {
        // round up value
        $factor = $this->math->pow('10', (string) $decimals);
        $value = ceil($value * $factor) / $factor;

        // format to fixed decimals
        return number_format($value, $decimals, '.', '');
    }

    /**
     * @return void
     */
    private function pushOperation(string $weekId, Operation $operation)
    {
        if (!isset(self::$operations[$operation->userId])) {
            self::$operations[$operation->userId] = [];
        }

        if (!isset(self::$operations[$operation->userId][$weekId])) {
            self::$operations[$operation->userId][$weekId] = [];
        }

        self::$operations[$operation->userId][$weekId][] = $operation;
    }

    /**
     * Generate week id.
     */
    private function weekId(\DateTime $datetime): string
    {
        $year = (int) $datetime->format('Y');

        // get rid of the last week of year
        $lastWeekStartDate = (new \DateTime())->setISODate($year, 53);
        if ($lastWeekStartDate->diff($datetime)->days < 6) {
            ++$year;
        }

        return $year.$datetime->format('W');
    }
}
