<?php

class Frame{

    protected $handler;
    protected $mode;
    public $data;

    public function __construct($mode,$data)
    {
        $this->data = $data;
        $files = serialize($data[0]);
        $stdInfos = serialize($data[1]);      
        if($mode){
            $shareData = $files.$stdInfos;

            $this->mem1Len = strlen($files);
            $this->mem2Len = strlen($stdInfos);

            $this->shmId = shmop_open(MEM_ADDR, 'c', 0667, $this->mem1Len+$this->mem2Len);
            shmop_write($this->shmId, $shareData, 0);
        }else{
            file_put_contents(FILEINFO, $files);
            file_put_contents(STDINFO, $stdInfos);
        }
        $this->mode = $mode;
    }

    protected function getProcessNum($fileCount)
    {
        $step = 1400;
        return (int)ceil($fileCount/$step);
    }

    //应该使用当前要处理的文件数量来智能划分任务 文件数量和进程数量 定义一个线性相关的函数即可
    public function mutiProc(){

        $fileCount = count($this->data[0]);
        $pro_num = $this->getProcessNum($fileCount);

        for($i = 0;$i<$pro_num;$i++){
            $command = 'php ./Worker/worker.php '.$i.' '.$pro_num;
            file_put_contents('frame.log',$command."\r\n",FILE_APPEND);
            $this->handler[] = popen($command,'r');   
        }   
    }

    function closeHandler()
    {
        foreach ($this->handler as $prc) {
            pclose($prc);
        }
    }

    function deleteCache()
    {
        if($this->mode){
            shmop_delete($this->shmId);
            shmop_close($this->shmId);
        }else{
            $files = scandir(getcwd());
            foreach ($files as $f) {
                if(pathinfo($f,PATHINFO_EXTENSION) === 'tmp'){
                   unlink($f); 
                }
            }          
        }
    }

    public function run()
    {
        $this->mutiProc();
        $this->closeHandler();
        $this->deleteCache();
    }

}