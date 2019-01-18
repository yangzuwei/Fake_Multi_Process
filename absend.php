<?php
require_once "./vendor/autoload.php";

if(php_sapi_name() === 'cli'){
    echo 'cli start'."\r\n";
}else{
    exit('Must on cli!'."\r\n");
}

$start = time();

$files = $files_had = [];

$file_path = 'E:\student\已处理-2018winter\20181228\_光州学校_';
$files = scanAll($file_path);
$files_had = imageNames($files);

$sql ='select id_num from student where school="光州学校" and is_handle=1';
$db = getLink();
$data = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$students = array_column($data,'id_num');

$diff = array_diff($students,$files_had);
var_dump($diff);