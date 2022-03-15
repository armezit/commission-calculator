<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Rules\Providers;

use Armezit\CommissionCalculator\Models\Operation;
use Armezit\CommissionCalculator\Service\Math;

class PrivateUserCommissionRule implements ExtensionProviderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $variables;

    /**
     * @var Math
     */
    private $math;

    /**
     * @var Operation
     */
    private $operation;

    /**
     * @var Operation[]
     */
    private $weekOperations;

    private static $regularFee = '0.003';
    private static $freeOfCharge = '1000';

    public function __construct(array $config, array $variables)
    {
        $this->config = $config;
        $this->variables = $variables;

        $this->math = new Math(2);
        $this->operation = $this->variables['op'];

        $week = $this->variables['week'];
        $weekOperations = $this->variables['ops'][$this->operation->userId][$week];
        $this->weekOperations = array_values(array_filter($weekOperations, function (Operation $op) {
            return $op->isWithdraw() && $op->isPrivateUser();
        }));
    }

    /**
     * @param $amount
     */
    private function calculateRegularFee($amount): string
    {
        // we need more precision, so set scale to 10
        return (new Math(10))->mul($amount, self::$regularFee);
    }

    /**
     * Execute rule.
     */
    public function execute(): string
    {
        $weekTotal = $this->weekTotal();
        $weekCount = $this->weekCount();

        if ($weekCount > 3) {
            return $this->calculateRegularFee($this->operation->baseAmount);
        }

        $isWeekTotalExceeded = $this->math->comp($weekTotal, self::$freeOfCharge) === 1;
        if (($isWeekTotalExceeded && $this->weekCount() <= 3) ||
            !$isWeekTotalExceeded && $this->weekCount() > 3) {
            // NOTE: up to 1000 is free of charge
            $comp = $this->math->comp($this->operation->baseAmount, self::$freeOfCharge);
            if ($comp === 1) {
                $amount = $this->math->sub($this->operation->baseAmount, self::$freeOfCharge);
            } elseif ($comp === -1) {
                $amount = $this->operation->baseAmount;
            } else {
                $amount = $this->math->sub($weekTotal, self::$freeOfCharge);
            }

            return $this->calculateRegularFee($amount);
        }

        return '0';
    }

    /**
     * Sum of the week operations base amounts.
     */
    private function weekTotal(): string
    {
        $amounts = array_map(function (Operation $op) {
            return $op->baseAmount ?: 0;
        }, $this->weekOperations);

        return (string) array_reduce($amounts, function ($a, $b) {
            return $this->math->add((string) $a, (string) $b);
        }, 0);
    }

    /**
     * Count of the week operations.
     */
    private function weekCount(): int
    {
        return count($this->weekOperations);
    }
}
