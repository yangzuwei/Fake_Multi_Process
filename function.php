<?php

//拿数据库中的信息
function getDb()
{
    $dsn = 'mysql:dbname=student;host=127.0.0.1';
    $user = 'root';
    $password = 'root';

    try {
        $dbh = new PDO($dsn, $user, $password);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    $dbh->query('set names gbk');
     
    $data = $dbh->query('select * from student');
    $dbh = null;

    $stdInfos = [];
    foreach ($data as $key => $value) {
        $stdInfos[$value['id_num']] = $value;
    }
    return $stdInfos;
}

//拿文件夹中的信息
function scanAll($dir, &$files)
{
   //$dir=iconv("utf-8","gb2312//IGNORE",$dir);
   if( strtoupper(pathinfo($dir, PATHINFO_EXTENSION)) == 'JPG' ){
       $files[] = $dir;
   }        
  if (is_dir($dir)){
    $children = scandir($dir);
    foreach ($children as $child){
      if ($child !== '.' && $child !== '..'){
        scanAll($dir.'/'.$child, $files);
      }
    }
  }
}
