#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

// load config
$config = require __DIR__ . '/config/app.php';

$csvFilepath = realpath($argv[1]);

// check if input file is accessible
if (!file_exists($csvFilepath) || !is_readable($csvFilepath)) {
    echo "File not found: $argv[1]\n";
    exit(1);
}
