#!/usr/bin/env php
<?php
use Warehouse\Warehouse;

$fullpath = __FILE__;
$projectRoot = realpath(dirname(__FILE__).'/../../');
$autoloadPath = $projectRoot.'/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    echo('Please run composer install before continuing.');
    exit(1);
}

include_once $autoloadPath;
$warehouse = new Warehouse();

if (count($argv) !== 3) {
    echo "  To use the CLI provide 2 arguments. 1st is the input CSV location.\n  Second is the output CSV location.\n";
    echo "  Example usage: $fullpath /tmp/input.csv /tmp/output.csv\n";
    exit(1);
}

$inputCsv = $argv[1];
$outputCsv = $argv[2];

if (!file_exists($inputCsv)) {
    echo "  Input CSV file provided does not exist. Please provide full path to a csv.\n";
    exit(1);
}

// Basic validation done, lets pass this over to Warehouse operation

try {
    $pickRun = $warehouse->getPickingRun($inputCsv);
    $warehouse->writeCsv($outputCsv, $pickRun);    
} catch (\Exception $ex) {
    echo "  Pick run generation failed with the following message: {$ex->getMessage()}\n";
    exit(1);
}

if ($warehouse->getPickRunWarnings()) {
    echo "  Output CSV written successfully, however there were warnings generated.\n";
    echo "  These are the warnings:\n";
    echo implode("\n\n", $warehouse->getPickRunWarnings());
    echo "  \n\nIf there are invalid entries in CVS please fix them an re-run them again.\n";
} else {
    echo "  Output CSV written successfully.\n";
}