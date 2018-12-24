<?php
require_once "./vendor/autoload.php";

set_error_handler("unlinkError");
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
function unlinkError(int $errno,string $errstr,string $errfile,int $errline)
{

    $logger = new Logger('unlink');
    $logger->pushHandler(new StreamHandler('./log/unhandler.log',Logger::WARNING));
    $logger->warning($errstr);
}

if(php_sapi_name() === 'cli'){
    echo 'Cli start'."\r\n";
}else{
    exit('Must on cli!'."\r\n");
}

$oldFiles = $newFiles = [];

$oldPath = 'E:\student\照片20181224';

$db = getLink();
$data = $db->query('select id_num from student where is_handle = 1')->fetchAll(PDO::FETCH_ASSOC);
$resNames = array_column($data,'id_num');

$sourceNames = scandir($oldPath);
unset($sourceNames[0]);
unset($sourceNames[1]);
array_walk($sourceNames,function(&$val){
    $val = explode('.',$val)[0];
});

$diff = array_diff($sourceNames,$resNames);

$diff2 = array_diff($resNames,$sourceNames);
var_dump($diff,$diff2);
