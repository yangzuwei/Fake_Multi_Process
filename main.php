<?php
$start = time();
echo '����ʼ����...'."\r\n";
$handler = [];
function run($start,$end){
    global $handler;
    for($i = $start;$i<$end;$i++){
        $handler[] = popen('php worker.php '.$i.' '.$end,'r');   
    }    
}

//������������ ��AMD X740 4��cpu������� ��7�������ٶ�������180-190s֮��
run(0,7);

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