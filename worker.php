<?php
require_once "student.php";
/**
 * argv数组代表的含义
 * $argv[1]:当前进程处理的任务段起始位置
 * $argv[2]:进程数量
 * 
 **
 * $argv[3]:share_memory 模式下 files文件的长度 默认从0开始存
 * $argv[4]:share_memory 模式下 stdinfos文件的长度 默认从files长度开始存
 */
if(isset($argv[3])){
    $std = new Student($argv[3], $argv[4]);
}else{
    $std = new Student();
}

$std->run($argv[1],$argv[2]);