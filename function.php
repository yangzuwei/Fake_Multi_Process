<?php

//�����ݿ��е���Ϣ
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

//���ļ����е���Ϣ �ݹ����
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
    //���ļ��е�����
    $dirStack = [$dir];
    do{
        //��ջ
        $dir = array_pop($dirStack);
        $tmpFiles = scandir($dir); 
        foreach ($tmpFiles as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            if($file === '.'||$file === '..')continue;
            if(is_dir($path)){
                //ѹջ
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

