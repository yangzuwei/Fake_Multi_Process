<?php
require_once('./Config/config.php');
require_once('./Util/function.php');
require_once('frame.php');
set_error_handler('errorHandler');
//ֻ����cliģʽ������
if(php_sapi_name() === 'cli'){
    echo 'cli start'."\r\n";
}else{
    exit('mode is wrong!'."\r\n");
}
$start = time();
$shareMode = SHARE_MODE?'memory':'file';
echo 'share mode is ...'.$shareMode."\r\n";


$files = [];

$file_path = "E:\student\/20180318";
//$file_path = "E:\����\ѧ����ӡ2016\ԭʼ��Ƭ���Ѿ�����";
scanAll($file_path,$files);

$data[0] = $files;
$data[1] = getDb();

$app = new Frame(SHARE_MODE,$data);

$app->run();

echo 'total time is'.(time()-$start).'s';