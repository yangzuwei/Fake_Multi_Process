#需求目标：单张2寸照片加入信息变成4张
###注意：在windows下，代码文件本身的编码必须是ansi格式的才能避免乱码。数据库（utf-8的字符集）执行的时候要写个query：set names gbk
ANSI 在中文操作系统中对应的字符集就是 gbk
##单个程序逻辑很简单
* 遍历目标文件夹下所有目标文件，存到数组中
* 从数据库中拿到所有的目标文件相应信息，存到数组中
* 根据目标文件相应的信息，在当前脚本文件的同级目录中建立文件夹存放图片处理结果
* gd库中的图片处理函数写的图片处理逻辑
## 图片处理文件说明：
先初始化一个真彩色的画布
将单个照片按照指定位置粘贴
将照片信息按照指定位置画写

初始化的时候将画布和蒙板都存入成员变量中，节省开销

文件IO共享/内存share memory
多进程    
## 项目文件说明
* main.php 入口文件 里面负责配置进程数量
* worker.php 子进程文件，实例化stduent对象，按照指定参数来运行
* student.php 核心逻辑文件，class student 里面处理数据
* function.php 依赖函数文件
* frame.php 运行流程框架文件
* config.php 配置文件

## 使用说明cli模式下 php main.php

新增了内存共享的模式来共享进程间数据，在config中进行配置。