<?php
//只能在cli模式下运行
if(php_sapi_name() === 'cli'){
    echo 'cli 模式启动'."\r\n";
}else{
    exit('本程序只能在cli模式下启动');
}
$start = time();
echo '程序开始启动...'."\r\n";
$handler = [];
function run($pro_num){
    global $handler;
    for($i = 0;$i<$pro_num;$i++){
        $handler[] = popen('php worker.php '.$i.' '.$pro_num,'r');   
    }    
}

//经过反复试验 在AMD X740 4核cpu的情况下 开7个进程速度是最快的180-190s之间
run(7);

foreach ($handler as $prc) {
    //echo fgets($prc);
    pclose($prc);
}

//删除缓存文件
$files = scandir(getcwd());
foreach ($files as $f) {
    if(pathinfo($f,PATHINFO_EXTENSION) === 'tmp'){
       unlink($f); 
    }
}

echo '共耗时'.(time()-$start).'s';