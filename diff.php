<?php
include './Util/function.php';

if(php_sapi_name() === 'cli'){
    echo 'cli start'."\r\n";
}else{
    exit('Must on cli!'."\r\n");
}

$oldFiles = $newFiles = [];

$oldPath = "E:\student".DIRECTORY_SEPARATOR."20180413";

scanAll($oldPath,$oldFiles);

$newPath = "E:\student".DIRECTORY_SEPARATOR."20180419";
scanAll($newPath,$newFiles);

foreach ($newFiles as $n) {
    if(in_array($oldPath.DIRECTORY_SEPARATOR.basename($n),$oldFiles)){
        unlink($n);
    }
}