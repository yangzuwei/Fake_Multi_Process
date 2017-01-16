<?php
function scanAll($dir, &$files)
{
    if(!is_dir($dir))return;
    //放文件夹的容器
    $dirStack = [$dir];
    do{
        //弹栈
        $dir = array_pop($dirStack);
        $tmpFiles = scandir($dir); 
        foreach ($tmpFiles as $file) {
            $path = $dir.DIRECTORY_SEPARATOR.$file;
            if($file === '.'||$file === '..')continue;
            if(is_dir($path)){
                //压栈
                array_push($dirStack, $path.DIRECTORY_SEPARATOR);
            }elseif(strtoupper(pathinfo($path, PATHINFO_EXTENSION)) == 'JPG' ){
                $files[] = $file;
            }
        }
    }while(!empty($dirStack));
    return;
}

//只能在cli模式下运行
if(php_sapi_name() === 'cli'){
    echo 'cli 模式启动'."\r\n";
}else{
    exit('本程序只能在cli模式下启动');
}
$start = time();

$files = $files_had = [];

$file_path = "E:\桌面\学籍验印2016\所有数据\src";
//$file_path = "E:\桌面\学籍验印2016\原始照片（已经处理）";
scanAll($file_path,$files);

$file_had_path = "E:\桌面\学籍验印2016\排版后照片";
scanAll($file_had_path,$files_had);

foreach ($files as $key => $value) {
    if(in_array($value, $files_had)){
        echo $value.'已经处理过'."\r\n";
    }
}

//echo '处理完成！共耗时'.(time()-$start).'s';