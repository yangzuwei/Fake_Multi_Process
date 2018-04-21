<?php
require_once('./Config/config.php');
require_once('./Util/function.php');
require_once('frame.php');
set_error_handler('errorHandler');

if(php_sapi_name() === 'cli'){
    echo 'cli start'."\r\n";
}else{
    exit('only for cli!'."\r\n");
}
$start = time();
$shareMode = SHARE_MODE?'memory':'file';
echo 'share mode is ...'.$shareMode."\r\n";

$files = [];

$file_path = "E:\student\/20180419";

scanAll($file_path,$files);

$data[0] = $files;
$data[1] = getDb();

$app = new Frame(SHARE_MODE,$data);

$app->run();

echo 'total time is'.(time()-$start).'s';