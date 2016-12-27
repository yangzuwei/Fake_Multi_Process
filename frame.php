<?php

class Frame{

    protected $handler;
    protected $mode;
    public $data;

    public function __construct($mode,&$data)
    {
        $files = serialize($data[0]);
        $stdInfos = serialize($data[1]);      
        if($mode){
            $shareData = $files.$stdInfos;

            $this->mem1Len = strlen($files);
            $this->mem2Len = strlen($stdInfos);
            //划定内存块
            $this->shmId = shmop_open(MEM_ADDR, 'c', 0667, $this->mem1Len+$this->mem2Len);
            shmop_write($this->shmId, $shareData, 0);
        }else{
            file_put_contents(FILEINFO, $files);
            file_put_contents(STDINFO, $stdInfos);
        }
        $this->mode = $mode;
    }


    //开启指定数目进程，将进程资源存到数组中
    public function mutiProc($pro_num){
        $otherArgs = '';
        if($this->mode){
            $otherArgs = $this->mem1Len.' '.$this->mem2Len;
        }

        for($i = 0;$i<$pro_num;$i++){
            $command = 'php worker.php '.$i.' '.$pro_num.' '.$otherArgs;   
            $this->handler[] = popen($command,'r');   
        }   
    }

    //关闭进程资源
    function closeHandler()
    {
        foreach ($this->handler as $prc) {
            pclose($prc);
        }
    }

    //删除缓存文件 或者释放
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
        $this->mutiProc(PROCESS_NUM);
        $this->closeHandler();
        $this->deleteCache();
    }

}