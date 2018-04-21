<?php
require_once "vendor/autoload.php";

if (isset($argv[3])) {
    $std = new \Worker\Student((int)$argv[3], (int)$argv[4]);
} else {
    $std = new \Worker\Student();
}

$std->run((int)$argv[1], (int)$argv[2]);