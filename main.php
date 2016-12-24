<?php
$start = time();
echo '程序开始启动...'."\r\n";
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

//删除缓存
unlink('stdinfo.tmp');
echo '共耗时'.(time()-$start).'s';