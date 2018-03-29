<?php
require_once "./vendor/autoload.php";

require_once(dirname(__DIR__)."/Config/config.php");
require_once(dirname(__DIR__)."/Util/function.php");

if(isset($argv[3])){
    $std = new \Worker\Student($argv[3], $argv[4]);
}else{
    $std = new \Worker\Student();
}

$std->run($argv[1],$argv[2]);