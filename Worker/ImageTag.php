<?php

namespace Worker;

class ImageTag
{

    public function __construct()
    {
        $this->students = getDb();
    }

    public function run($dir)
    {
        $files = scanAll($dir);
        foreach ($files as $file) {
            $stdUnique = explode('.', basename($file))[0];
            $this->imageTag($file, $this->students[$stdUnique]);
        }
    }

    //加入版权等信息
    protected function iptcMakeTag($rec, $data, $value)
    {
        $length = strlen($value);
        $retval = chr(0x1C) . chr($rec) . chr($data);

        if ($length < 0x8000) {
            $retval .= chr($length >> 8) . chr($length & 0xFF);
        } else {
            $retval .= chr(0x80) .
                chr(0x04) .
                chr(($length >> 24) & 0xFF) .
                chr(($length >> 16) & 0xFF) .
                chr(($length >> 8) & 0xFF) .
                chr($length & 0xFF);
        }

        return $retval . $value;
    }

    protected function imageTag($path, $student)
    {
        $this->clearEndInfo($path);
        $year = (int)date('Y');
        $iptc = array(
            '2#120' => $student['std_name'],
            '2#116' => 'Copyright 永久,'. iconv("UTF-8", "GBK", COPYRIGHT),
            '2#025' => $student['school'] . $student['class'],
            '2#080' => iconv("UTF-8", "GBK", AUTHOR),
        );

        // Convert the IPTC tags into binary code
        $data = '';

        foreach ($iptc as $tag => $string) {
            $tag = substr($tag, 2);
            $data .= $this->iptcMakeTag(2, $tag, $string);
        }
        // Embed the IPTC data
        $content = iptcembed($data, $path);
        $fp = fopen($path, 'wb');
        $secret = base64_encode(serialize($student));
        fwrite($fp, $content.$secret);
        fclose($fp);
    }

    protected function clearEndInfo($path)
    {
        $fp = fopen($path, "a+b");
        $newSize = filesize($path) + $this->getEndPosition($fp) + 2;
        rewind($fp);
        $newContent = fread($fp, $newSize);
        fclose($fp);
        $fp = fopen($path, 'wb');
        fwrite($fp, $newContent);
        fclose($fp);
    }

    public function getEndInfo($path)
    {
        $fp = fopen($path, "a+b");
        $position = $this->getEndPosition($fp);
        fseek($fp, $position + 2, SEEK_END);
        $newSize = abs($position);
        $content = fread($fp, $newSize);
        fclose($fp);
        return $content;
    }

    protected function getEndPosition($fp)
    {
        //从文件末尾查找jpg文件末尾标识
        $target = pack("CC", 0xff, 0xd9);
        $search = '';
        $i = 0;
        fseek($fp, --$i, SEEK_END);
        $last = fgetc($fp);

        while ($search != $target) {
            fseek($fp, --$i, SEEK_END);
            $c = fgetc($fp);
            $search = $c . $last;
            $last = $c;
        }
        return $i;
    }
}