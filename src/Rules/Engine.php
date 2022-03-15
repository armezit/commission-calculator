<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Rules;

class Engine
{
    /**
     * @var ExpressionLanguage
     */
    private $el;
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->el = new ExpressionLanguage(null, [
            new ExtensionExpressionFunctionProvider($this->config),
        ]);
    }

    public function execute(array $variables): string
    {
        return $this->process($variables, $this->config['rule_engine']['rules']);
    }

    /**
     * Recursively process rules with the given variables.
     *
     * @param $rules
     */
    private function process(array $variables, $rules): string
    {
        foreach ($rules as $condition => $expr) {
            // if condition is not true, ignore it and continue to other rules
            if (!$this->evaluate($variables, $condition)) {
                continue;
            }
            // condition evaluated, but it has sub-rules
            if (is_array($expr)) {
                return $this->process($variables, $expr);
            }
            // finally, return expression result
            return (string) $this->evaluate($variables, $expr);
        }

        return '0';
    }

    /**
     * Evaluate expression with the given variables and return it`s result.
     *
     * @return mixed
     */
    private function evaluate(array $variables, string $expression)
    {
        return $this->el->evaluate($expression, $variables);
    }
}
