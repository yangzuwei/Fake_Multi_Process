<?php
include './Util/function.php';

if(php_sapi_name() === 'cli'){
    echo 'cli start'.PHP_EOL;
}else{
    exit('Must on cli!'.PHP_EOL);
}

$oldFiles = $newFiles = [];

$oldPath = 'E:\student\students_image_rebuild\result\20181228';

$oldFiles = scanAll($oldPath);

$newPath = 'E:\student\dup';
$newFiles = scanAll($newPath);
$srcs = [];
foreach ($newFiles as $f){
    $srcs[] = basename($f);
}

foreach ($oldFiles as $n) {
    $shortPath = strtoupper(basename($n));
    //var_dump($shortPath,$n);exit();
    if(in_array($shortPath,$srcs)){
        $full = $newPath.DIRECTORY_SEPARATOR.$shortPath;
        //var_dump($full);exit();
        unlink($full);
    }
}