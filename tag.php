<?php
require_once "./vendor/autoload.php";

$dir = 'E:\student'.DIRECTORY_SEPARATOR.'20180318-sample';

$tagWorker = new \Worker\ImageTag();
$tagWorker->run($dir);
//$info = $tagWorker->getEndInfo($dir.DIRECTORY_SEPARATOR.'X.JPG');
//var_dump($info);