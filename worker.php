<?php
require_once "student.php";
if(isset($argv[3])){
    $std = new Student($argv[3], $argv[4]);
}else{
    $std = new Student();
}

$std->run($argv[1],$argv[2]);