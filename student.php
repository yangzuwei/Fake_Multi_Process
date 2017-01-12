<?php

require_once('config.php');
require_once('function.php');

class Student
{
    public $files;
    public $stdInfos;

    public function __construct($mem1Len = 0,$mem2Len = 0){

        //初始化图片背景和蒙板
        $this->initImage();
        //判断是什么模式 默认内存共享
        if (SHARE_MODE) {
            $shmId = shmop_open(MEM_ADDR, 'c', 0667, $mem1Len+$mem2Len);
            $data1 = shmop_read($shmId, 0, $mem1Len);
            $data2 = shmop_read($shmId, $mem1Len, $mem2Len);
            $this->files = unserialize($data1);
            $this->stdInfos = unserialize($data2);
        }else{
            $this->getFiles();
            $this->getStuInfo();
        }
        //判断是否是处理文件的模式 如果是则调用处理函数 如果不是则直接复制文件到对应的目录中
        $this->handPic = IS_HANDLE?'productImage':'copyImage';

    }

    protected function initImage()
    {
        $bg_w = $this->mask_w = 480; // 背景图片宽度  
        $bg_h    = 672; // 背景图片高度  
        $this->background = imagecreatetruecolor($bg_w,$bg_h); // 背景图片  
        $white   = imagecolorallocate($this->background, 255, 255, 255); // 为真彩色画布创建白色背景，再设置为透明  
        imagefill($this->background, 0, 0, $white);  
        imageColorTransparent($this->background, $white); 
        $font = 'C:\Windows\Fonts\simhei.ttf'; 
        $fontSize = 14;
        $fontY = imagefontheight($fontSize);
        $this->mask_h = 3*$fontY + 10;
        $this->mask = imagecreatetruecolor($this->mask_w,$this->mask_h);
        imagefill($this->mask, 0, 0, $white);         
    }

    //用于文件缓存
    protected function getFiles()
    {
        //先从文件里面读缓存
        if(file_exists(FILEINFO) && $tmp = unserialize(file_get_contents(FILEINFO))){
            $this->files = $tmp;
        }else{
            exit('程序出错，无法获取照片源文件名称');
        }

    }

    //用于文件缓存
    protected function getStuInfo()
    {
        //先从文件里面读缓存
        if(file_exists(STDINFO) && $tmp = unserialize(file_get_contents(STDINFO))){
            $this->stdInfos = $tmp;
        }else{
            //如果没有就从数据库里面拿
            $this->stdInfos = getDb();            
        }

    }

    public function run($argStart,$partNum)
    {
          
        //将要处理的文件数组分成n份
        $totalNum = count($this->files);
        $everyPartNum = intval($totalNum/$partNum)+1;

        //从接受的argv[1] 参数中获得该份的标识头数字
         
        //先从数据库中把学生的 身份证号 姓名 学籍辅号 学校 班级信息 拿出来

        //var_dump($stdInfos);exit();
        //echo '程序开始启动……'."\r\n";
        $time = time();
        
        //echo '当前部分处理学生照片总量：'.$everyPartNum;
        $startNum = $argStart*$everyPartNum;
        $endNum = ($startNum+$everyPartNum)>$totalNum?$totalNum:($startNum+$everyPartNum);

        $this->exePart($startNum, $endNum);
        // foreach ($this->exePart($startNum, $endNum) as $value) {
        //     echo $value;
        // }

        imagedestroy($this->background);
        imagedestroy($this->mask);
        //echo '程序运行完毕。总耗时：'.time()-$time;
        //echo "完成第".($argStart+1)."部分\r\n";
    }

    public function exePart($startNum, $endNum)
    {
        $handPic = $this->handPic;
        for ($i = $startNum; $i< $endNum; $i++) {
            $stdPicPath = $this->files[$i];

            $stdIdNum = basename($stdPicPath, '.JPG');

            if( isset($this->stdInfos[$stdIdNum]) === false ){
                continue;
            }
            //在当前目录下建立对应的文件夹（如果不存在）
            if(!is_dir(getcwd().DS."res")){
                mkdir(getcwd().DS."res");
            }
            $school = $this->stdInfos[$stdIdNum]['school'];
            if(!is_dir(getcwd().DS.'res'.DS.$school)){
                mkdir(getcwd().DS.'res'.DS.$school);
            }
            $class  = $this->stdInfos[$stdIdNum]['class'];
            if(!is_dir(getcwd().DS.'res'.DS.$school.DS.$class)){
                mkdir(getcwd().DS.'res'.DS.$school.DS.$class);
            }
            $targetPath = getcwd().DS.'res'.DS.$school.DS.$class.DS.$stdIdNum.'.JPG';
            
            //此处不够严谨 应该判断一下图像文件是否完整合法
            $this->$handPic($stdPicPath,$this->stdInfos[$stdIdNum],$targetPath);
            unset($this->stdInfos[$stdIdNum]);
            unset($this->files[$i]);
            //echo '当前完成数量：'.$i.'/'.$everyPartNum."\r\n";
        }        
    }


    protected function copyImage($imgPath,$stdInfo,$targetPath)
    {
        return copy($imgPath, $targetPath);
    }

    protected function productImage($imgPath,$stdInfo,$targetPath)
    {
        $pic_list = array(  
            $imgPath,  
            $imgPath, 
            $imgPath,   
            $imgPath, 
        );

        $lineArr    = array();  // 需要换行的位置  
        $space_x    = 44;  
        $space_y    = 105;  
        $line_x  = 10;  

        //单张照片
        $start_x    = 32;    // 开始位置X  
        $start_y    = 18;    // 开始位置Y  
        $pic_w   = 188;//intval($bg_w/2) - 5; // 宽度  
        $pic_h   = 232;//intval($bg_h/2) - 5; // 高度  
        $lineArr = array(3);  
        $line_x  = 32;  

        foreach( $pic_list as $k=>$pic_path ) {  
            $kk = $k + 1;  
            if ( in_array($kk, $lineArr) ) {  
                $start_x    = $line_x;  
                $start_y    = $start_y + $pic_h + $space_y;  
            }  
            $pathInfo = pathinfo($pic_path);  
            switch( strtolower($pathInfo['extension']) ) {  
                case 'jpg':  
                case 'jpeg':  
                    $imagecreatefromjpeg    = 'imagecreatefromjpeg';  
                break;  
                case 'png':  
                    $imagecreatefromjpeg    = 'imagecreatefrompng';  
                break;  
                case 'gif':  
                default:  
                    $imagecreatefromjpeg    = 'imagecreatefromstring';    
                break;  
            }

            // try{
            //     $resource   = @$imagecreatefromjpeg($pic_path);
            //     if (!$resource) {
            //          throw new \Exception("照片文件损坏");  
            //      }
            // }catch(\Excption $e){
            //     echo $e->message();
            //     file_put_contents('error.log', $e->message());
            // }finally{
            //     return false;
            // }  
 
            $black = imagecolorallocate($this->background, 0, 0, 0);
            $white = imagecolorallocate($this->background, 255, 255, 255);
            // The text to draw
            $idNum = strlen($stdInfo['id_num'])<19?$stdInfo['id_num']:"";//'411522198512086610';
            $stdName = iconv('gbk', 'utf-8', $stdInfo['std_name']);//"司马";
            $auxNum = $stdInfo['aux_num'];//"2016155222030001";
            // Replace path by your own font path
            $font = 'C:\Windows\Fonts\simhei.ttf'; 
            $fontSize = 14;
            $fontY = imagefontheight($fontSize);
            $fontX = imagefontwidth($fontSize);

            imagettftext($this->background,$fontSize, 0, $start_x+($pic_w - (strlen($idNum)+2)*$fontX)/2, $start_y+$pic_h+$fontY, $black, $font, $idNum);
            imagettftext($this->background,$fontSize, 0, $start_x+($pic_w - (strlen($stdName)-3)*$fontX)/2, $start_y+$pic_h+2*$fontY+3, $black, $font, $stdName);
            imagettftext($this->background,$fontSize, 0, $start_x+($pic_w - (strlen($auxNum)+2)*$fontX)/2, $start_y+$pic_h+3*$fontY+6, $black, $font, $auxNum);

            imagecopyresized($this->background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource));
            $start_x = $start_x + $pic_w + $space_x;  
        } 
        imagejpeg($this->background,$targetPath);

        //用白色蒙板图片盖住文字部分 然后这个背景图又可以重复利用了
        $start_mask_y = 18+232;
        imagecopyresized($this->background,$this->mask,0,$start_mask_y,0,0,$this->mask_w,$this->mask_h,imagesx($this->background),imagesy($this->mask));
        $start_mask_y = 18+232*2+105;
        imagecopyresized($this->background,$this->mask,0,$start_mask_y,0,0,$this->mask_w,$this->mask_h,imagesx($this->background),imagesy($this->mask));
        return true;
        //imagedestroy($background);
    }

}

  
      