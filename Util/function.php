<?php

function getLink()
{
    $dsn = 'mysql:dbname=student;host=127.0.0.1';
    $user = 'root';
    $password = 'root';
    $dbh = null;

    try {
        $dbh = new PDO($dsn, $user, $password);
        $dbh->query('set names utf8');
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }

    return $dbh;
}

function getDb()
{
    $dbh = getLink();
    $data = $dbh->query('select * from student')->fetchAll(PDO::FETCH_ASSOC);
    $dbh = null;

    $stdInfos = [];
    foreach ($data as $key => $value) {
        $id = $value['id_num'];//iconv('gbk', 'utf-8', $value['id_num']);
        $stdInfos[$id] = $value;
    }
    return $stdInfos;
}

function scanAll($dir)
{
    $files = [];
    if (!is_dir($dir)) {
        return [];
    }
    $dirStack = [$dir];
    while (!empty($dirStack)) {
        $dir = array_pop($dirStack);
        $tmpFiles = scandir($dir);
        foreach ($tmpFiles as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if ($file === '.' || $file === '..') continue;
            if (is_dir($path)) {
                array_push($dirStack, $path . DIRECTORY_SEPARATOR);
            } elseif (strtoupper(pathinfo($path, PATHINFO_EXTENSION)) == 'JPG') {
                $files[] = $path;
            }
        }
    }
    return $files;
}

function imageNames($fullPaths)
{
    $names = [];
    foreach ($fullPaths as $path) {
        $names[] = basename($path, '.jpg');
    }
    return $names;
}

function errorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
    return file_put_contents('log/error.log', $errno . $errmsg . $filename . $linenum . $vars . PHP_EOL);
}

