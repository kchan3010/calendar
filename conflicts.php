<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "autoload.php";
use Classes\conflictCalendar;


array_shift($argv);
if (count($argv) == 0) {
    $argv = "php://stdin";
}
$entries = explode("\n", file_get_contents($argv[0], "r"));

try{
    $calendar = new conflictCalendar($entries);

    $response = $calendar->get_conflicts();

    print_r($response);
} catch (Exception $e) {
    //can do some logging
    echo $e->getMessage();
}
