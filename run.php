<?php
require_once 'autoloader.php';

if (isset($argv[1])) {
    $filename = $argv[1];
    $processor = new ProductListProcessor();
    $processor->setRequiredFields(['make', 'model']);
    $processor->processFile($filename); 
} else {
    echo "\n================\n";
    echo "Something went wrong!";
    echo "\n================\n";
}
?>
