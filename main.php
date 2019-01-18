<?php
require_once "./vendor/autoload.php";
require_once('frame.php');
set_error_handler('errorHandler');

if (php_sapi_name() === 'cli') {
    echo 'cli start' . PHP_EOL;
} else {
    exit('only for cli!' . PHP_EOL);
}
$start = time();
$shareMode = SHARE_MODE ? 'memory' : 'file';
echo 'share mode is ...' . $shareMode . PHP_EOL;

$files = [];

$file_path = 'E:\student\照片原始文件\0113';

$files = scanAll($file_path);

foreach ($files as $k => $f) {
    $pathInfo = pathinfo($f);
    $newName = $pathInfo['dirname'].DIRECTORY_SEPARATOR.strtoupper($pathInfo['basename']);
    rename($f, $newName);
    //var_dump($newName);exit();
    $files[$k] = $newName;
}

$data[0] = $files;
$data[1] = getDb();

$app = new Frame($data);
$app->run();

echo 'total time is ' . (time() - $start) . 's';


//核查检验 照片文件名称和数据库是否一致 将不在数据库中的照片路径打印出来

$fileNameOfSrc = imageNames($files);
$invalidIDs = array_diff($fileNameOfSrc, array_keys($data[1]));

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('check');
$log->pushHandler(new StreamHandler('log/invalidIds.log', Logger::NOTICE));

$log->warning('未明学籍学生一共' . count($invalidIDs) . '人', []);
foreach ($invalidIDs as $id) {
    $log->addNotice('数据库中未发现：' . $id);
}