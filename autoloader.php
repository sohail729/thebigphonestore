<?php

if (version_compare(phpversion(), 7, '<')) {
    throw new Exception("PHP version 7 or higher is required.");
}

function autoloader($class) {
    $file = __DIR__ . '/classes/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        include_once $file;
    }
}

spl_autoload_register('autoloader');
