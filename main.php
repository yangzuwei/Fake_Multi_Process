<?php
$start = time();
echo '����ʼ����...'."\r\n";
$handler = [];
function run($start,$end){
    global $handler;
    for($i = $start;$i<$end;$i++){
        $handler[] = popen('php student.php '.$i.' '.$end,'r');    
    }    
}

// foreach (run(0,5) as $r) {
//     echo $r."\n";
// }
run(0,5);

foreach ($handler as $key => $value) {
    pclose($value);
}

echo '����ʱ'.(time()-$start).'s';