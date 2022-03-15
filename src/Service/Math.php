<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Service;

class Math
{
    /**
     * @var int
     */
    private $scale;

    public function __construct(int $scale)
    {
        $this->scale = $scale;
    }

    /**
     * @return $this
     */
    public function setScale(int $scale): Math
    {
        $this->scale = $scale;

        return $this;
    }

    public function add(string $leftOperand, string $rightOperand): string
    {
        return bcadd($leftOperand, $rightOperand, $this->scale);
    }

    public function sub(string $leftOperand, string $rightOperand): string
    {
        return bcsub($leftOperand, $rightOperand, $this->scale);
    }

    public function mul(string $leftOperand, string $rightOperand): string
    {
        return bcmul($leftOperand, $rightOperand, $this->scale);
    }

    public function div(string $leftOperand, string $rightOperand): string
    {
        return bcdiv($leftOperand, $rightOperand, $this->scale);
    }

    public function comp(string $leftOperand, string $rightOperand): int
    {
        return bccomp($leftOperand, $rightOperand, $this->scale);
    }

    public function pow(string $num, string $exponent): string
    {
        return bcpow($num, $exponent, $this->scale);
    }
}
