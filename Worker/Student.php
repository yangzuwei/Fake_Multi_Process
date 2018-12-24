<?php

namespace Worker;

use Monolog\Logger;

class Student
{
    public $files;
    public $stdInfos;
    protected $logger;
    static $link;

    public function __construct($mem1Len = 0, $mem2Len = 0)
    {
        $this->logger = new Logger('unhandler');
        self::$link = getLink();

        //判断是什么模式 默认内存共享
        if (SHARE_MODE) {
            $shmId = shmop_open(MEM_ADDR, 'c', 0667, $mem1Len + $mem2Len);
            $data1 = shmop_read($shmId, 0, $mem1Len);
            $data2 = shmop_read($shmId, $mem1Len, $mem2Len);
            $this->files = unserialize($data1);
            $this->stdInfos = unserialize($data2);
        } else {
            $this->getFiles();

            $data = self::$link->query('select * from student');
            $stdInfoTmp = [];
            foreach ($data as $key => $value) {
                $id = $value['id_num'];//iconv('gbk', 'utf-8', $value['id_num']);
                $stdInfoTmp[$id] = $value;
            }

            $this->stdInfos = $stdInfoTmp;
        }
    }

    //用于文件缓存
    protected function getFiles()
    {
        //先从文件里面读缓存
        if (file_exists(FILEINFO) && $tmp = unserialize(file_get_contents(FILEINFO))) {
            $this->files = $tmp;
        } else {
            exit('程序出错，无法获取照片源文件名称');
        }
    }

    public function run($argStart, $partNum)
    {
        $this->customDivide($argStart, $partNum);
    }

    protected function customDivide($argStart, $partNum)
    {
        //将要处理的文件数组分成n份
        $totalNum = count($this->files);
        $everyPartNum = intval($totalNum / $partNum) + 1;

        //echo '当前部分处理学生照片总量：'.$everyPartNum;
        $startNum = $argStart * $everyPartNum;
        $endNum = ($startNum + $everyPartNum) > $totalNum ? $totalNum : ($startNum + $everyPartNum);
        $this->exePart($startNum, $endNum);
    }

    //执行处理文件数组的一个部分参数为数组下标起始值
    public function exePart($startNum, $endNum)
    {
        $producer = new ImageProducer();
        for ($i = $startNum; $i < $endNum; $i++) {
            $stdPicPath = $this->files[$i];
            $stdIdNum = explode('.', basename($stdPicPath))[0];

            if (isset($this->stdInfos[$stdIdNum]) === false) {
                //日志记录映射失败的信息
                $this->logger->info("can not find the {$stdIdNum} in the stdInfos map",[]);
                continue;
            }

            $targetPath = $this->makeDirAndTarget($stdIdNum);

            //判断是否是处理文件的模式 如果是则调用处理函数 如果不是则直接复制文件到对应的目录中
            if(IS_HANDLE){
                //照片底下的三行字
                $pictureText = [
                    $this->stdInfos[$stdIdNum]['id_num'],
                    $this->stdInfos[$stdIdNum]['std_name'],
                    $this->stdInfos[$stdIdNum]['aux_num']
                ];

                $producer->productPicture($stdPicPath, $pictureText, $targetPath);
                //将处理后的学生标识为已经处理
                self::$link->query('update student set is_handle = 1 where aux_num = ' . $this->stdInfos[$stdIdNum]['aux_num']);
            }else{
                copy($stdPicPath, $targetPath);
            }
        }
    }

    protected function makeDirAndTarget($stdIdNum)
    {
        $dirArr = [];
        $school = '_'.$this->stdInfos[$stdIdNum]['school'].'_';//iconv("UTF-8", "GBK",);
        $class =  '_'.$this->stdInfos[$stdIdNum]['class'].'_';//iconv("UTF-8", "GBK", );
        //在当前目录下建立对应的文件夹（如果不存在）
        $file = getcwd() . DS
            . "result" . DS
            . date('Ymd'). DS
            . $school. DS
            . $class. DS
            .$stdIdNum . '.jpg';

        $tmpDir = dirname($file);

        while (!is_dir($tmpDir)){
            array_push($dirArr, $tmpDir);
            $tmpDir = dirname($tmpDir);
        }

        while(count($dirArr)){
            mkdir(array_pop($dirArr));
        }

        return $file;
    }
}

  
      