<?php
include './Util/function.php';

if(php_sapi_name() === 'cli'){
    echo 'cli start'.PHP_EOL;
}else{
    exit('Must on cli!'.PHP_EOL);
}

$oldFiles = $newFiles = [];

$oldPath = "E:\student".DIRECTORY_SEPARATOR."20180413";

$oldFiles = scanAll($oldPath);

$newPath = "E:\student".DIRECTORY_SEPARATOR."20180419";
$newFiles = scanAll($newPath);

foreach ($newFiles as $n) {
    if(in_array($oldPath.DIRECTORY_SEPARATOR.basename($n),$oldFiles)){
        unlink($n);
    }
}