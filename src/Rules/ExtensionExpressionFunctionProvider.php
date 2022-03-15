<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Rules;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Default expression language provider which registers "ext()" function into our rule engine.
 */
class ExtensionExpressionFunctionProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $extensions;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->extensions = $this->config['rule_engine']['extensions'];
    }

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('ext', function ($arg) {
                return sprintf('new $this->extensions[%s]($this->config)', $arg);
            }, function (array $variables, $value) {
                return new $this->extensions[$value]($this->config, $variables);
            }),
        ];
    }
}
