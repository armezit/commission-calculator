<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Service;

class DataValidator
{
    /**
     * @param $value
     * @param $filter
     * @param $options
     *
     * @return mixed
     */
    public function execute($value, $filter, $options)
    {
        return filter_var($value, $filter, $options);
    }
}
