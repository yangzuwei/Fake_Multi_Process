<?php
require_once "student.php";
/**
 * argv�������ĺ���
 * $argv[1]:��ǰ���̴�����������ʼλ��
 * $argv[2]:��������
 * 
 **
 * $argv[3]:share_memory ģʽ�� files�ļ��ĳ��� Ĭ�ϴ�0��ʼ��
 * $argv[4]:share_memory ģʽ�� stdinfos�ļ��ĳ��� Ĭ�ϴ�files���ȿ�ʼ��
 */
if(isset($argv[3])){
    $std = new Student($argv[3], $argv[4]);
}else{
    $std = new Student();
}

$std->run($argv[1],$argv[2]);