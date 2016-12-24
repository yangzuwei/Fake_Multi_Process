<?php
define('DS', DIRECTORY_SEPARATOR);
class Student
{
    public $files;
    public $dir;

    public function __construct($dir){
        $this->dir = $dir;
    }

    /**
     * 遍历目标文件夹 取得文件绝对路径
     */
    public function scanAll($dir)
    {
       //$dir=iconv("utf-8","gb2312//IGNORE",$dir);
       if( strtoupper(pathinfo($dir, PATHINFO_EXTENSION)) == 'JPG' ){
           $this->files[] = $dir;
       }        
      if (is_dir($dir)){
        $children = scandir($dir);
        foreach ($children as $child){
          if ($child !== '.' && $child !== '..'){
            $this->scanAll($dir.'/'.$child);
          }
        }
      }
    }

    public function getStuInfo()
    {
        //先从文件里面读缓存
        if(file_exists('stdinfo.tmp') && $tmp = file_get_contents('stdinfo.tmp')){
            return unserialize($tmp);
        }
        //如果没有就从数据库里面拿
        $dsn = 'mysql:dbname=student;host=127.0.0.1';
        $user = 'root';
        $password = 'root';

        try {
            $dbh = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        $dbh->query('set names gbk');
         
        $data = $dbh->query('select * from student');
        $stdInfos = [];
        foreach ($data as $key => $value) {
            $stdInfos[$value['id_num']] = $value;
        }
        file_put_contents('stdinfo.tmp', serialize($stdInfos));
        return $stdInfos;
    }

    public function run($argStart,$partNum)
    {
        $this->scanAll($this->dir);
        //将要处理的文件数组分成10份
        $totalNum = count($this->files);
        $everyPartNum = intval($totalNum/$partNum)+1;

        //从接受的argv[1] 参数中获得该份的标识头数字
         
        //先从数据库中把学生的 身份证号 姓名 学籍辅号 学校 班级信息 拿出来
        $stdInfos = $this->getStuInfo();
        //var_dump($stdInfos);exit();
        //echo '程序开始启动……'."\r\n";
        $time = time();
        
        //echo '当前部分处理学生照片总量：'.$everyPartNum;
        $startNum = $argStart*$everyPartNum;
        $endNum = ($startNum+$everyPartNum)>$totalNum?$totalNum:($startNum+$everyPartNum);
        for ($i = $startNum; $i< $endNum; $i++) {
            $stdPicPath = $this->files[$i];

            $stdIdNum = basename($stdPicPath, '.JPG');
            if( isset($stdInfos[$stdIdNum]) === false ){
                continue;
            }
            //在当前目录下建立对应的文件夹（如果不存在）
            if(!is_dir(dirname(__FILE__).DS."res")){
                mkdir(dirname(__FILE__).DS."res");
            }
            $school = $stdInfos[$stdIdNum]['school'];
            if(!is_dir(dirname(__FILE__).DS.'res'.DS.$school)){
                mkdir(dirname(__FILE__).DS.'res'.DS.$school);
            }
            $class  = $stdInfos[$stdIdNum]['class'];
            if(!is_dir(dirname(__FILE__).DS.'res'.DS.$school.DS.$class)){
                mkdir(dirname(__FILE__).DS.'res'.DS.$school.DS.$class);
            }
            $targetPath = dirname(__FILE__).DS.'res'.DS.$school.DS.$class.DS.$stdIdNum.'.JPG';
            $this->productImage($stdPicPath,$stdInfos[$stdIdNum],$targetPath);
            unset($stdInfos[$stdIdNum]);
            unset($this->files[$i]);
            //echo '当前完成数量：'.$i.'/'.$everyPartNum."\r\n";
        }

        //echo '程序运行完毕。总耗时：'.time()-$time;
        echo "完成第".($argStart+1)."部分\r\n";
    }


    public function productImage($imgPath,$stdInfo,$targetPath)
    {
        $pic_list = array(  
            $imgPath,  
            $imgPath, 
            $imgPath,   
            $imgPath, 
        );

        $bg_w    = 480; // 背景图片宽度  
        $bg_h    = 672; // 背景图片高度  

        $background = imagecreatetruecolor($bg_w,$bg_h); // 背景图片  
        $white   = imagecolorallocate($background, 255, 255, 255); // 为真彩色画布创建白色背景，再设置为透明  
        imagefill($background, 0, 0, $white);  
        imageColorTransparent($background, $white); 

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
                    $pic_path    = file_get_contents($pic_path);  
                break;  
            }  
            $resource   = $imagecreatefromjpeg($pic_path); 
            $black = imagecolorallocate($background, 0, 0, 0);
            // The text to draw
            $idNum = strlen($stdInfo['id_num'])<19?$stdInfo['id_num']:"";//'411522198512086610';
            $stdName = iconv('gbk', 'utf-8', $stdInfo['std_name']);//"司马";
            $auxNum = $stdInfo['aux_num'];//"2016155222030001";
            // Replace path by your own font path
            $font = 'C:\Windows\Fonts\simhei.ttf'; 
            $fontSize = 14;
            $fontY = imagefontheight($fontSize);
            $fontX = imagefontwidth($fontSize);

            imagettftext($background,$fontSize, 0, $start_x+($pic_w - (strlen($idNum)+2)*$fontX)/2, $start_y+$pic_h+$fontY, $black, $font, $idNum);
            imagettftext($background,$fontSize, 0, $start_x+($pic_w - (strlen($stdName)-3)*$fontX)/2, $start_y+$pic_h+2*$fontY+3, $black, $font, $stdName);
            imagettftext($background,$fontSize, 0, $start_x+($pic_w - (strlen($auxNum)+2)*$fontX)/2, $start_y+$pic_h+3*$fontY+6, $black, $font, $auxNum);

            imagecopyresized($background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource));
            $start_x = $start_x + $pic_w + $space_x;  
        } 
        imagejpeg($background,$targetPath);
        imagedestroy($background);
    }

}

  
      