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
     * ����Ŀ���ļ��� ȡ���ļ�����·��
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
        //�ȴ��ļ����������
        if(file_exists('stdinfo.tmp') && $tmp = file_get_contents('stdinfo.tmp')){
            return unserialize($tmp);
        }
        //���û�оʹ����ݿ�������
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
        //��Ҫ������ļ�����ֳ�10��
        $totalNum = count($this->files);
        $everyPartNum = intval($totalNum/$partNum)+1;

        //�ӽ��ܵ�argv[1] �����л�ø÷ݵı�ʶͷ����
         
        //�ȴ����ݿ��а�ѧ���� ���֤�� ���� ѧ������ ѧУ �༶��Ϣ �ó���
        $stdInfos = $this->getStuInfo();
        //var_dump($stdInfos);exit();
        //echo '����ʼ��������'."\r\n";
        $time = time();
        
        //echo '��ǰ���ִ���ѧ����Ƭ������'.$everyPartNum;
        $startNum = $argStart*$everyPartNum;
        $endNum = ($startNum+$everyPartNum)>$totalNum?$totalNum:($startNum+$everyPartNum);
        for ($i = $startNum; $i< $endNum; $i++) {
            $stdPicPath = $this->files[$i];

            $stdIdNum = basename($stdPicPath, '.JPG');
            if( isset($stdInfos[$stdIdNum]) === false ){
                continue;
            }
            //�ڵ�ǰĿ¼�½�����Ӧ���ļ��У���������ڣ�
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
            //echo '��ǰ���������'.$i.'/'.$everyPartNum."\r\n";
        }

        //echo '����������ϡ��ܺ�ʱ��'.time()-$time;
        echo "��ɵ�".($argStart+1)."����\r\n";
    }


    public function productImage($imgPath,$stdInfo,$targetPath)
    {
        $pic_list = array(  
            $imgPath,  
            $imgPath, 
            $imgPath,   
            $imgPath, 
        );

        $bg_w    = 480; // ����ͼƬ���  
        $bg_h    = 672; // ����ͼƬ�߶�  

        $background = imagecreatetruecolor($bg_w,$bg_h); // ����ͼƬ  
        $white   = imagecolorallocate($background, 255, 255, 255); // Ϊ���ɫ����������ɫ������������Ϊ͸��  
        imagefill($background, 0, 0, $white);  
        imageColorTransparent($background, $white); 

        $lineArr    = array();  // ��Ҫ���е�λ��  
        $space_x    = 44;  
        $space_y    = 105;  
        $line_x  = 10;  

        //������Ƭ
        $start_x    = 32;    // ��ʼλ��X  
        $start_y    = 18;    // ��ʼλ��Y  
        $pic_w   = 188;//intval($bg_w/2) - 5; // ���  
        $pic_h   = 232;//intval($bg_h/2) - 5; // �߶�  
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
            $stdName = iconv('gbk', 'utf-8', $stdInfo['std_name']);//"˾��";
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

  
      