<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Models;

class Operation
{
    /**
     * @var \DateTime
     */
    public $date;

    /**
     * @var string
     */
    public $userId;

    /**
     * @var string
     */
    public $userType;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $amount;

    /**
     * @var string
     */
    public $baseAmount;

    /**
     * @var string
     */
    public $currency;

    public function __construct(
        \DateTime $date,
        string $userId,
        string $userType,
        string $type,
        string $amount,
        string $currency
    ) {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->type = $type;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function isDeposit(): bool
    {
        return $this->type === 'deposit';
    }

    public function isWithdraw(): bool
    {
        return $this->type === 'withdraw';
    }

    public function isPrivateUser(): bool
    {
        return $this->userType === 'private';
    }

    public function isBusinessUser(): bool
    {
        return $this->userType === 'business';
    }
}
