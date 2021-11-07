<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/11/2
 * Time: 22:47
 */

namespace pc\temp_replace;

use phpDocumentor\Reflection\Types\Self_;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;


class BatchTempReplaceTest extends TestCase
{


    public static $fs;

    protected static $test_path="";
    protected static $orig_path="";

    public static function setUpBeforeClass(): void
    {
        self::$fs = new Filesystem();
        self::$test_path="test_material/test";
        self::$orig_path="test_material/test_ex";
        self::init();
    }

    public static function init(){

        self::$fs->remove(self::$test_path);
        self::$fs->mirror(self::$orig_path, self::$test_path);
    }

    public static function tearDownAfterClass(): void
    {
        //dump("结束");
       self::$fs->remove(self::$test_path);
    }

    public function testGenerate()
    {
        $test = new BatchTempReplace();
       self::init();
        //测试内容替换
        $t=[
            "aaaa"=>"a1a1a1",
            "cccc"=>"c1c1c1"
        ];
        $test->init()->setOriginalDir(self::$test_path)
            ->setStringReplaceMap($t)
            ->generate(true);
        self::assertFileNotEquals(self::$test_path."/a1/qqq/ccc.txt",self::$orig_path."/a1/qqq/ccc.txt");
        self::assertFileNotEquals(self::$test_path."/weerwr.txt",self::$orig_path."/weerwr.txt");
        $a=file_get_contents(self::$test_path."/a1/qqq/ccc.txt");
        $b=file_get_contents(self::$orig_path."/a1/qqq/ccc.txt");
        self::assertEquals(str_replace(array_keys($t),array_values($t),$b),$a);

        self::init();
        //测试标题替换
        $t=[
           "qqq"=>"xxx",
           "ccc"=>"c1c1c1"
        ];
        $test->init()->setOriginalDir(self::$test_path)
            ->setTitleReplaceMap($t)
            ->generate(true);
        self::directoryExists(self::$test_path."/a2/xxx");
        self::fileExists(self::$test_path."/a2/xxx/c1c1c1.txt");
        self::fileExists(self::$test_path."/a2/xxx.txt");

        self::init();
        //测试path
        $test->init()->setOriginalDir(self::$test_path)->setPath(["a1/","a2/qqq"])->setStringReplaceMap([
            "aaaa"=>"1111"
        ])->generate();
        self::assertFileEquals(self::$test_path."/a2/www/fff.txt",self::$orig_path."/a2/www/fff.txt");
        self::assertFileNotEquals(self::$test_path."/a2/qqq.txt",self::$orig_path."/a2/qqq.txt");
        self::assertFileNotEquals(self::$test_path."/a1/qqq.txt",self::$orig_path."/a1/qqq.txt");
        self::assertFileNotEquals(self::$test_path."/a1/qqq/ccc.txt",self::$orig_path."/a1/qqq/ccc.txt");

        self::init();
        //测试notpath
        $test->init()->setOriginalDir(self::$test_path)->setNotPath(["a1/","a2/qqq"])->setStringReplaceMap([
            "aaaa"=>"1111"
        ])->generate();
        self::assertFileEquals(self::$test_path."/a2/qqq.txt",self::$orig_path."/a2/qqq.txt");
        self::assertFileEquals(self::$test_path."/a1/qqq.txt",self::$orig_path."/a1/qqq.txt");
        self::assertFileEquals(self::$test_path."/a1/qqq/ccc.txt",self::$orig_path."/a1/qqq/ccc.txt");



    }


}
