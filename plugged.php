<?php
$directory = __DIR__;

$plugged = [
    'core/cnf/core.cnf.php',
    'core/tools/autoload.php',
    'core/tools/functions.php'
];

foreach ($plugged as $plug) {

    $path = $directory . "/" . $plug;
    if (file_exists($path)) {

        require_once $path;

    }

}