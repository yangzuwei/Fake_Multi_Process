<?php
define('STDINFO', 'stdinfo.tmp');//学生信息文件（来源mysql）
define('FILEINFO', 'files.tmp');//学生照片路径文件（来源scandir）
define('DS', DIRECTORY_SEPARATOR);
define('SHARE_MODE', false);//true为共享内存模式false为文件缓存共享模式
define('MEM_ADDR', 0x12345); //共享内存模式下 内存起始地址
define('PROCESS_NUM', 1); //开启进程数目
define('IS_HANDLE',true);//是否处理文件默认处理 否则的话原样归档整理

ini_set('date.timezone','PRC');
define('COPYRIGHT',', 河南省光山县教体局');
define('AUTHOR','基教股');