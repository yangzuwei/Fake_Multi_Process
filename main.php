<?php
//ֻ����cliģʽ������
if(php_sapi_name() === 'cli'){
    echo 'cli ģʽ����'."\r\n";
}else{
    exit('������ֻ����cliģʽ������');
}
$start = time();
echo '����ʼ����...'."\r\n";
$handler = [];
function run($pro_num){
    global $handler;
    for($i = 0;$i<$pro_num;$i++){
        $handler[] = popen('php worker.php '.$i.' '.$pro_num,'r');   
    }    
}

//������������ ��AMD X740 4��cpu������� ��7�������ٶ�������180-190s֮��
run(7);

foreach ($handler as $prc) {
    //echo fgets($prc);
    pclose($prc);
}

//ɾ�������ļ�
$files = scandir(getcwd());
foreach ($files as $f) {
    if(pathinfo($f,PATHINFO_EXTENSION) === 'tmp'){
       unlink($f); 
    }
}

echo '����ʱ'.(time()-$start).'s';