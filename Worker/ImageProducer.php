<?php

namespace Worker;

class ImageProducer
{
	protected $canvas;
	protected $textCanvas;
	protected $bigCanvas;
	protected $fontY;
	protected $fontX;

	const FONT = 'C:\Windows\Fonts\simhei.ttf'; 
	const FONT_SIZE = 13;
	const MARGIN_WIDTH = 5;//25;
	const MARGIN_HEIGHT = 2;//15;
	const PIC_WIDTH = 175.5;//191;
	const PIC_HEIGHT = 216;//233;
	const BIG_CANVAS_WIDTH = 371;//480;
	const BIG_CANVAS_HEIGHT = 580;//672;

	public function __construct()
	{
		$this->setFont();
		$this->initCanvas();
	}

	public function setFont()
	{
		$this->fontY = imagefontheight(self::FONT_SIZE);
		$this->fontX = imagefontwidth(self::FONT_SIZE);	
	}

	protected function initCanvas()
	{
		$canvasWidth = self::MARGIN_WIDTH*2 + self::PIC_WIDTH;
		$textArea = (8 + $this->fontY ) * 3;
		$canvasHeight = self::MARGIN_HEIGHT*2 + self::PIC_HEIGHT + $textArea;
		$this->canvas = imagecreatetruecolor($canvasWidth, $canvasHeight);
		$this->bigCanvas = imagecreatetruecolor(self::BIG_CANVAS_WIDTH, self::BIG_CANVAS_HEIGHT);

        $this->textCanvas = imagecreatetruecolor(self::BIG_CANVAS_WIDTH, $textArea);
        $white = imagecolorallocate($this->canvas, 0xff, 0xff, 0xff);
        imagefill($this->textCanvas, 0, 0, $white);

	}

	protected function fillWhite()
	{
		$white = imagecolorallocate($this->canvas, 0xff, 0xff, 0xff);
		imagefill($this->canvas, 0, 0, $white);
		imagefill($this->bigCanvas, 0, 0, $white);
        //覆盖文字
        imagecopyresized($this->canvas,$this->textCanvas,0,self::MARGIN_HEIGHT + self::PIC_HEIGHT,0,0,self::BIG_CANVAS_WIDTH, self::BIG_CANVAS_HEIGHT,imagesx($this->textCanvas), imagesy($this->textCanvas));
	}

    protected function getImageResource($picturePath)
    {
        switch (exif_imagetype($picturePath)) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($picturePath);
                break;
            case IMAGETYPE_PNG:
                return imagecreatefrompng($picturePath);
                break;
            case IMAGETYPE_BMP:
                return imagecreatefrombmp($picturePath);
                break;
            default:
                return imagecreatefromstring($picturePath);
                break;
        }
    }

	public function setPicture($picturePath)
	{
		$pic = $this->getImageResource($picturePath);
		imagecopyresized($this->canvas, $pic, self::MARGIN_WIDTH, self::MARGIN_HEIGHT, 0, 0, self::PIC_WIDTH ,self::PIC_HEIGHT, imagesx($pic), imagesy($pic));		
	}

	public function writeLineText(int $line,string $text)
	{
		$black = imagecolorallocate($this->canvas, 0x00, 0x00, 0x00);

		$indent = 0;//单行缩进
        $lineHeight = 4;//行高

		if($line == 0){
            $text = strlen($text)<19?$text:"";//'411522200112120312';
        }

		if($line == 1){
            $indent = -3;//单行缩进 学生姓名
        }

		$offsetX = self::MARGIN_WIDTH + (self::PIC_WIDTH - (strlen($text)+$indent)*$this->fontX)/2;
		$offsetY = self::PIC_HEIGHT + self::MARGIN_HEIGHT + ($line+1)*($this->fontY+$lineHeight);
		imagefttext($this->canvas, self::FONT_SIZE, 0, $offsetX, $offsetY, $black, self::FONT, $text);
	}

	public function productPicture($picturePath,$pictureText,$destination)
	{
		$this->fillWhite();
		$this->setPicture($picturePath);
		$i = 0;
		//写三行字
		foreach ($pictureText as $value) {
			$this->writeLineText($i,$value);
			$i++;
		}
		//复制2行2列四张图
		$this->copyMore(2,2);
        imagejpeg($this->bigCanvas, $destination);
	}

	protected function copyMore($colum,$row)
	{
		$srcWidth = imagesx($this->canvas);
		$srcHeight = imagesy($this->canvas);
		for($i = 0;$i<$colum;$i++){
			for($j=0;$j<$row;$j++){
				imagecopyresized($this->bigCanvas,$this->canvas, $i*$srcWidth, $j*$srcHeight, 0, 0, $srcWidth, $srcHeight, $srcWidth, $srcHeight);
			}
		}
	}
}
