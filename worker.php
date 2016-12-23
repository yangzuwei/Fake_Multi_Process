<?php
require_once "student.php";
$std = new Student("E:\桌面\学籍验印2016\所有数据\src");
$std->run($argv[1],$argv[2]);