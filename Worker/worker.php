<?php
require_once "./vendor/autoload.php";

if(isset($argv[3])){
    $std = new \Worker\Student($argv[3], $argv[4]);
}else{
    $std = new \Worker\Student();
}

$std->run($argv[1],$argv[2]);