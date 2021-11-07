<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/10/26
 * Time: 22:10
 */

namespace pcj\temp_replace;


use pcj\temp_replace\Exception\TempReplaceException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class BatchTempReplace
{
    protected $path = [];
    protected $not_path = [];


    //字符替换map
    protected $string_replace_map = [];

    //目录或文件替换map
    protected $title_replace_map = [];

    //源目录
    protected $original_dir = null;
    //生成目标目录
    protected $new_dir_name = null;


    /**
     * @param array $string_replace_map
     */
    public function setStringReplaceMap($string_replace_map)
    {
        $this->string_replace_map = $string_replace_map;
        return $this;
    }

    /**
     * @param array $title_replace_map
     */
    public function setTitleReplaceMap($title_replace_map)
    {
        $this->title_replace_map = $title_replace_map;
        return $this;
    }

    /**
     * @param null $original_dir
     */
    public function setOriginalDir($original_dir)
    {
        $this->original_dir = $original_dir;
        return $this;
    }

    /**
     * @param null $new_dir_name
     */
    public function setNewDirName($new_dir_name)
    {
        $this->new_dir_name = $new_dir_name;
        return $this;
    }


    public function __construct()
    {
        $this->tempReplace = new TempReplace();
    }


    protected function check()
    {
        if ($this->original_dir == "") {
            throw new TempReplaceException("未设置源路径");
        }
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param mixed $not_path
     */
    public function setNotPath($not_path)
    {
        $this->not_path = $not_path;
        return $this;

    }

    public function generate($force = true)
    {
        $new_dir_name = $this->new_dir_name ?: $this->original_dir;
        $fs = new Filesystem();

        if (realpath($new_dir_name) != realpath($this->original_dir)) {
            $fs->mirror($this->original_dir, $new_dir_name, null, ["override" => $force]);
        }


        if ($this->path) {
            $files = (new Finder())->in($new_dir_name)->notPath($this->not_path)->path($this->path)->files();
        } else {
            $files = (new Finder())->in($new_dir_name)->notPath($this->not_path)->files();
        }
        $r = new TempReplace();

        foreach ($files as $v) {
            $r->setOriginalFile($v->getRealPath())->setStringReplaceMap($this->string_replace_map)->generate();
        }


        $sort = function (\SplFileInfo $a, \SplFileInfo $b) {
            //dump('--',$a->getPathname(),$b->getPathname(),'==');
            return strlen($b->getRealPath()) - strlen($a->getRealPath());
        };
        if ($this->path) {
            $dirs = (new Finder())->in($new_dir_name)->notPath($this->not_path)->path($this->path)->sort($sort)->directories();
        } else {
            $dirs = (new Finder())->in($new_dir_name)->notPath($this->not_path)->sort($sort)->directories();
        }

        foreach ($dirs as $v) {
            $fs->rename($v->getRealPath(), rtrim($v->getRealPath(), $v->getFilename()) . str_replace(array_keys($this->title_replace_map), array_values($this->title_replace_map), $v->getFilename()), $force);
        }


    }

    public function init()
    {
        $this->path = [];
        $this->not_path = [];
        $this->title_replace_map = [];
        $this->string_replace_map = [];
        $this->original_dir = null;
        $this->new_dir_name = null;
        return $this;
    }
}