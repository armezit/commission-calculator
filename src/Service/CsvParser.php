<?php

declare(strict_types=1);

namespace Armezit\CommissionCalculator\Service;

/**
 * Parse given csv file contents into an array.
 */
class CsvParser
{
    /**
     * @var array
     */
    public $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function execute(string $csvFilepath): \Generator
    {
        $validator = new DataValidator();

        $useValidator = $this->config['data']['use_validator'];
        $dataSchema = $this->config['data']['schema'];

        try {
            $fh = fopen($csvFilepath, 'r');
            $line = 0;

            // loop over csv rows
            while (($row = fgetcsv($fh)) !== false) {
                $i = 0;
                $record = [];
                foreach ($dataSchema as $field => $validationRule) {
                    // validate data
                    if ($useValidator && $validator->execute($row[$i], $validationRule[0], $validationRule[1]) === false) {
                        throw new \RuntimeException("Invalid data received at line $line");
                    }
                    $record[$field] = $row[$i];
                    ++$i;
                }

                yield $record;
                ++$line;
            }
        } finally {
            fclose($fh);
        }
    }
}
