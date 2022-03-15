# Paysera Commission task skeleton

# Run

Run `cli` script in the project`s root directory:

```shell
./cli input.csv
```

or 

```shell
php cli input.csv
```

# Tests

Run all project tests (phpunit and php-cs-fixer):

```shell
composer run test
```

Run only phpunit tests:

```shell
composer run phpunit
```

# Config

We use [Symfony ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html)
to define a simple rule engine. Rules could be defined in the `config/app.php`.

Rules are in format `condition` => `expression`.
Each evaluated condition results to the corresponding expression.

**Simple rule:**

```php
...
'rule_engine' => [
    'rules' => [
        'op.isDeposit()' => 'op.amount * (0.03 / 100)',
        ...
  ]
],
```

**Cascading rules:**

Cascading rules would be evaluated recursively:

```php
...
'rule_engine' => [
    'rules' => [
        'op.isWithdraw()' => [
            'op.isBusinessUser()' => 'op.amount * (0.5 / 100)'
            ...
        ],
        ...
  ]
],
```

**Complex rules:**

Complex rules are standalone rule classes and can be defined in config:

```php
...
'rule_engine' => [
    'extensions' => [
        'pu' => PrivateUserCommissionRule::class,
    ],
    'rules' => [
        'op.isWithdraw()' => [
            'op.isPrivateUser()' => 'ext("pu").execute()',
            ...
        ],
        ...
  ]
],
```

