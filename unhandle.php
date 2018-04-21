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

$oldPath = "E:\student\/origin\/20180509/";//"E:\student".DIRECTORY_SEPARATOR."20180413";

$db = getLink();
$data = $db->query('select id_num from student where is_handle = 1')->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $item) {
    $fullPath = $oldPath.$item['id_num'].'.JPG';
    unlink($fullPath);
}
