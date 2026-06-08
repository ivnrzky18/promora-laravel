<?php
require 'vendor/autoload.php';

// List all Eris generator classes
$dir = 'vendor/giorgiosironi/eris/src/Generator';
if (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        if (str_ends_with($file, '.php')) {
            echo $file . PHP_EOL;
        }
    }
}
