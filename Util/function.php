<?php

function getLink()
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
    return $dbh;
}

function getDb()
{
    $dbh = getLink();
    $data = $dbh->query('select * from student')->fetchAll(PDO::FETCH_ASSOC);
    $dbh = null;

    $stdInfos = [];
    foreach ($data as $key => $value) {
        $stdInfos[$value['id_num']] = $value;
    }
    return $stdInfos;
}

// function scanAll($dir, &$files)
// {
//    //$dir=iconv("utf-8","gb2312//IGNORE",$dir);
//    if( strtoupper(pathinfo($dir, PATHINFO_EXTENSION)) == 'JPG' ){
//        $files[] = $dir;
//    }        
//   if (is_dir($dir)){
//     $children = scandir($dir);
//     foreach ($children as $child){
//       if ($child !== '.' && $child !== '..'){
//         scanAll($dir.'/'.$child, $files);
//       }
//     }
//   }
// }

//���ļ����еľ�����Ϣ �ǵݹ�
function scanAll($dir, &$files)
{
    if(!is_dir($dir))return;
    $dirStack = [$dir];
    do{
        $dir = array_pop($dirStack);
        $tmpFiles = scandir($dir); 
        foreach ($tmpFiles as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            if($file === '.'||$file === '..')continue;
            if(is_dir($path)){
                array_push($dirStack, $path.DIRECTORY_SEPARATOR);
            }elseif(strtoupper(pathinfo($path, PATHINFO_EXTENSION)) == 'JPG' ){
                $files[] = $path;
            }
        }
    }while(!empty($dirStack));
    return;
}

function errorHandler($errno, $errmsg, $filename, $linenum, $vars)  
{
    return file_put_contents('error.log', $errno.$errmsg.$filename.$linenum.$vars."\r\n") ;

}

