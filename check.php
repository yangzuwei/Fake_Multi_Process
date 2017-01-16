<?php
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
                $files[] = $file;
            }
        }
    }while(!empty($dirStack));
    return;
}

//ֻ����cliģʽ������
if(php_sapi_name() === 'cli'){
    echo 'cli ģʽ����'."\r\n";
}else{
    exit('������ֻ����cliģʽ������');
}
$start = time();

$files = $files_had = [];

$file_path = "E:\����\ѧ����ӡ2016\��������\src";
//$file_path = "E:\����\ѧ����ӡ2016\ԭʼ��Ƭ���Ѿ�����";
scanAll($file_path,$files);

$file_had_path = "E:\����\ѧ����ӡ2016\�Ű����Ƭ";
scanAll($file_had_path,$files_had);

foreach ($files as $key => $value) {
    if(in_array($value, $files_had)){
        echo $value.'�Ѿ������'."\r\n";
    }
}

//echo '������ɣ�����ʱ'.(time()-$start).'s';