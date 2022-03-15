<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Rules\Providers;

interface ExtensionProviderInterface
{
    public function __construct(array $config, array $variables);
}
