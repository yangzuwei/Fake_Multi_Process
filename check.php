<?php
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
                $files[] = $file;
            }
        }
    }while(!empty($dirStack));
    return;
}

if(php_sapi_name() === 'cli'){
    echo 'cli start'."\r\n";
}else{
    exit('Must on cli!'."\r\n");
}

$start = time();

$files = $files_had = [];

$file_path = "E:\src";

scanAll($file_path,$files);

$file_had_path = "E:\des";
scanAll($file_had_path,$files_had);

foreach ($files as $key => $value) {
    if(in_array($value, $files_had)){
        echo $value.' already done '."\r\n";
    }
}
