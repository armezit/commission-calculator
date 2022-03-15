<?php

use Armezit\CommissionCalculator\Rules\Providers\PrivateUserCommissionRule;

return [
    'data' => [
        // if we should validate input data or not
        'use_validator' => true,
        // associative array of field names as key, and validator rule (for filter_var) as value
        'schema' => [
            // sample: 2014-12-31,4,private,withdraw,1200.00,EUR
            'date' => [FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/\d{4}-\d{2}-\d{2}/']]],
            'user_id' => [FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => PHP_INT_MAX]]],
            'user_type' => [FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/private|business/']]],
            'type' => [FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/deposit|withdraw/']]],
            'amount' => [FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0, 'max_range' => PHP_FLOAT_MAX]]],
            'currency' => [FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/EUR|USD|JPY/']]],
        ]
    ],
    'base_currency' => 'EUR',
    'currencies' => [
        'EUR' => ['decimals' => 2,],
        'JPY' => ['decimals' => 0,],
        'USD' => ['decimals' => 2,],
    ],
    'rule_engine' => [
        'extensions' => [
            'pu' => PrivateUserCommissionRule::class,
        ],
        'rules' => [
            'op.isDeposit()' => 'op.amount * (0.03 / 100)',
            'op.isWithdraw()' => [
                'op.isPrivateUser()' => 'ext("pu").execute()',
                'op.isBusinessUser()' => 'op.amount * (0.5 / 100)'
            ],
        ]
    ],
];