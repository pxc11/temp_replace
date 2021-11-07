<?php
include("vendor/autoload.php");
$test = new \pc\temp_replace\TempReplace();//单文件内容替换
$test->setOriginalFile("xxx.txt")//设置原文件(路径)
->setNewFileName("aaa.txt")//设置新文件(路径)可不设置，不设置直接更改原文件
->setStringReplaceMap([
    "aaa" => "bbb",
    "ccc" => "ddd"
])//设置替换内容
->generate();//生成  效果是生成aaa.txt，文件内容“aaa”替换为"bbb"

$test = new \pc\temp_replace\BatchTempReplace();//多文件替换 还可文件目录名替换
$test->setOriginalDir("test") //设置原目录
->setNewDirName("test1") //设置生成目录 可不设置
->setPath([
    "test1/aa",
    "test1/bb"
])//设置只匹配上述条件下进行替换,可不设置
->setNotPath([
    "test1/cc",
    "test1/dd"
])//设置忽略匹配上述条件下进行替换,可不设置
->setStringReplaceMap([
    "aaa" => "bbb",
    "ccc" => "ddd"
])//设置替换内容
->setTitleReplaceMap([
    "aa.txt" => "aa.tmp",
    "dir1" => "dir2"
])//目录,文件名替换
->generate();//生成一个目录，里面是替换好内容的文件与替换好名称的目录与文件
