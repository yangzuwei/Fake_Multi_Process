<?php
require_once('config.php');
require_once('function.php');
require_once('frame.php');
//ֻ����cliģʽ������
if(php_sapi_name() === 'cli'){
    echo 'cli ģʽ����'."\r\n";
}else{
    exit('������ֻ����cliģʽ������');
}
$start = time();
$shareMode = SHARE_MODE?'�����ڴ�':'�����ļ�';
echo '����ʼ����...'.$shareMode.'ģʽ�´���'."\r\n";


$files = [];

$file_path = "E:\����\ѧ����ӡ2016\��������\src";
scanAll($file_path,$files);

$data[0] = $files;
$data[1] = getDb();

$app = new Frame(SHARE_MODE,$data);

$app->run();

echo '������ɣ�����ʱ'.(time()-$start).'s';