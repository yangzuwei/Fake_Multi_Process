<?php
require_once('config.php');
require_once('function.php');
require_once('frame.php');
//只能在cli模式下运行
if(php_sapi_name() === 'cli'){
    echo 'cli 模式启动'."\r\n";
}else{
    exit('本程序只能在cli模式下启动');
}
$start = time();
$shareMode = SHARE_MODE?'共享内存':'共享文件';
echo '程序开始启动...'.$shareMode.'模式下处理'."\r\n";


$files = [];

$file_path = "E:\桌面\学籍验印2016\所有数据\src";
scanAll($file_path,$files);

$data[0] = $files;
$data[1] = getDb();

$app = new Frame(SHARE_MODE,$data);

$app->run();

echo '处理完成！共耗时'.(time()-$start).'s';