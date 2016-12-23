<?php
$start = time();
echo '程序开始启动...'."\r\n";
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

echo '共耗时'.(time()-$start).'s';