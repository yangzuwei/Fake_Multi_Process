#需求目标：单张2寸照片加入信息变成4张

##单个程序逻辑很简单
* 遍历目标文件夹下所有目标文件，存到数组中
* 从数据库中拿到所有的目标文件相应信息，存到数组中
* 根据目标文件相应的信息，在当前脚本文件的同级目录中建立文件夹存放图片处理结果
* gd库中的图片处理函数写的图片处理逻辑
## 图片处理文件说明：
- 使用三个真彩画布,运行过程中不销毁，重复利用
- 将单个照片按照指定位置粘贴
- 将照片信息按照指定位置画写
 
## 项目文件说明
* main.php 入口文件 里面负责配置进程数量
* worker.php 子进程文件，实例化tduent对象，按照指定参数来运行
* Student.php 分块处理信息
* ImageProducer.php 图片合成器
* function.php 依赖函数文件
* frame.php 运行流程框架文件
* config.php 配置文件

## 使用说明cli模式下 php main.php

#### 附：
新增了内存共享的模式来共享进程间数据，在config中进行配置。
文件IO共享/内存share memory(非必要，纯粹是玩一下的)