<?php
require_once "./vendor/autoload.php";

if(php_sapi_name() === 'cli'){
    echo 'cli start'."\r\n";
}else{
    exit('Must on cli!'."\r\n");
}

$start = time();

$files = $files_had = [];

$file_path = 'E:\student\排版后\20180417';

$files = scanAll($file_path);

var_dump(count($files));
$file_had_path = "E:\des";
$files_had = scanAll($file_had_path);

foreach ($files as $key => $value) {
    if(in_array($value, $files_had)){
        echo $value.' already done '."\r\n";
    }
}
