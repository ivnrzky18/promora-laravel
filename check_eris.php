<?php
require 'vendor/autoload.php';

$items = [
    'Eris\TestTrait',
    'Eris\Facade',
    'Eris\Generator\StringGenerator',
    'Eris\Generator\ElementsGenerator',
    'Eris\Generator\IntegerGenerator',
    'Eris\Generator\AlphaStringGenerator',
    'Eris\Generator\PrintableStringGenerator',
];

foreach ($items as $item) {
    $exists = class_exists($item) || interface_exists($item) || trait_exists($item);
    echo $item . ': ' . ($exists ? 'YES' : 'NO') . PHP_EOL;
}
