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

// foreach (run(0,5) as $r) {
//     echo $r."\n";
// }
run(0,4);

foreach ($handler as $key => $value) {
    pclose($value);
}

//ɾ������
unlink('stdinfo.tmp');
echo '����ʱ'.(time()-$start).'s';