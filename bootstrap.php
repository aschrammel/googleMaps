<?php
if (version_compare(phpversion(), '7.0', '<')) {
    echo 'This library requires at least PHP 7.0. Your version: ' . phpversion();
    exit(1);
}

require_once 'vendor/autoload.php';
